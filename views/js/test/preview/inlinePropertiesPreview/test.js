/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA ;
 */
define(['context', 'jquery'], function (context) {
    'use strict';

    const moduleId = 'taoItems/preview/inlinePropertiesPreview';
    const requestModuleId = 'core/request';
    const urlModuleId = 'util/url';
    const readyTimeoutMs = 15000;
    const itemUri = 'item://test-item';

    const config = {
        isPreviewEnabled: true,
        itemUri
    };

    function setupDom() {
        const tag = 'd' + 'iv';
        $('#qunit-fixture').html(
            '<' + tag + ' id="item-properties-form-column" class="item-properties-column item-properties-column--form"></' + tag + '>' +
            '<' + tag + ' id="item-properties-preview-column" class="item-properties-column item-properties-column--preview">' +
            '<' + tag + ' id="item-properties-preview" class="item-properties-preview"></' + tag + '>' +
            '</' + tag + '>'
        );
    }

    function enableExternalPreviewer() {
        context.previewerExternalFeUrl = window.location.origin;
        context.featureFlags = context.featureFlags || {};
        context.featureFlags.FEATURE_FLAG_TAO_ADVANCE_EXTERNAL_ITEM_PREVIEWER = true;
        context.locale = 'en-US';
    }

    function loadModule(options = {}) {
        const requestCalls = [];
        const postMessages = [];

        define(requestModuleId, [], function () {
            return function (payload) {
                requestCalls.push(payload);
                if (options.requestReject) {
                    return Promise.reject(options.requestReject);
                }
                if (options.requestResponse) {
                    return Promise.resolve(options.requestResponse);
                }
                return Promise.resolve({
                    success: true,
                    data: {
                        refresh_token: options.refreshToken || 'refresh-token',
                        access_token: options.accessToken || 'access-token'
                    }
                });
            };
        });

        define(urlModuleId, [], function () {
            return {
                route() {
                    return '/get-tokens';
                }
            };
        });

        return new Promise((resolve, reject) => {
            requirejs.undef(moduleId);
            require([moduleId], function (preview) {
                resolve({ preview, requestCalls, postMessages });
            }, reject);
        });
    }

    function flushAsync() {
        return new Promise(resolve => setTimeout(resolve, 0));
    }

    function patchReadyTimeout(run) {
        const originalSetTimeout = window.setTimeout;

        window.setTimeout = function (handler, delay) {
            if (delay === readyTimeoutMs) {
                return originalSetTimeout(handler, 5);
            }
            return originalSetTimeout(handler, delay);
        };

        return Promise.resolve()
            .then(run)
            .finally(() => {
                window.setTimeout = originalSetTimeout;
            });
    }

    function dispatchMessage(iframe, data) {
        window.dispatchEvent(
            new MessageEvent('message', {
                origin: window.location.origin,
                source: iframe.contentWindow,
                data
            })
        );
    }

    QUnit.module('inlinePropertiesPreview', {
        beforeEach() {
            this.originalExternalUrl = context.previewerExternalFeUrl;
            this.originalFeatureFlags = context.featureFlags;
            this.originalLocale = context.locale;
            setupDom();
            enableExternalPreviewer();
        },
        afterEach() {
            [
                moduleId,
                requestModuleId,
                urlModuleId
            ].forEach(id => requirejs.undef(id));

            context.previewerExternalFeUrl = this.originalExternalUrl;
            context.featureFlags = this.originalFeatureFlags;
            context.locale = this.originalLocale;
            $('#qunit-fixture').empty();
        }
    });

    QUnit.test('hides preview panel when external previewer is unavailable', function (assert) {
        const done = assert.async();

        context.featureFlags.FEATURE_FLAG_TAO_ADVANCE_EXTERNAL_ITEM_PREVIEWER = false;

        loadModule()
            .then(({ preview }) => {
                preview.init(config);

                assert.ok($('#item-properties-preview-column').is(':hidden'), 'Preview column is hidden');
                assert.ok(
                    $('#item-properties-form-column').hasClass('item-properties-column--full'),
                    'Form column expands to full width'
                );
                done();
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('hides preview panel when previewer URL is invalid', function (assert) {
        const done = assert.async();

        context.previewerExternalFeUrl = 'not-a-valid-url';

        loadModule()
            .then(({ preview }) => {
                preview.init(config);

                assert.ok($('#item-properties-preview-column').is(':hidden'), 'Preview column is hidden');
                done();
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('shows panel error when token fetch fails', function (assert) {
        const done = assert.async();

        loadModule({ requestReject: new Error('token failed') })
            .then(({ preview, requestCalls }) => {
                preview.init(config);

                return flushAsync().then(() => {
                    assert.equal(requestCalls.length, 1, 'Token request is called once');
                    assert.equal(
                        $('#item-properties-preview .item-properties-preview-error').text(),
                        'Unable to load item preview',
                        'Token failure shows an error in the preview panel'
                    );
                    done();
                });
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('shows timeout error when preview never becomes ready', function (assert) {
        const done = assert.async();

        patchReadyTimeout(() =>
            loadModule().then(({ preview, requestCalls }) => {
                preview.init(config);

                return flushAsync()
                    .then(() => {
                        assert.equal(requestCalls.length, 1, 'Token request is called once');
                        assert.ok(
                            document.querySelector('.item-properties-preview-iframe'),
                            'Iframe is mounted before timeout'
                        );
                    })
                    .then(() => new Promise(resolve => setTimeout(resolve, 20)))
                    .then(() => {
                        assert.equal(
                            $('#item-properties-preview .item-properties-preview-error').text(),
                            'External previewer load timeout',
                            'Timeout shows an error in the preview panel'
                        );
                        done();
                    });
            })
        ).catch(err => {
            assert.ok(false, err && err.message ? err.message : String(err));
            done();
        });
    });

    QUnit.test('performs token handshake and reveals iframe on ready', function (assert) {
        const done = assert.async();
        const postMessages = [];

        loadModule({
            refreshToken: 'refresh-123',
            accessToken: 'access-123'
        })
            .then(({ preview, requestCalls }) => {
                preview.init(config);

                return flushAsync().then(() => {
                    const iframe = document.querySelector('.item-properties-preview-iframe');

                    assert.equal(requestCalls.length, 1, 'Token request is called once');
                    assert.ok(iframe, 'Iframe is mounted in the preview panel');
                    assert.ok(iframe.classList.contains('visually-hidden'), 'Iframe is hidden until ready');

                    const iframeUrl = new URL(iframe.src);
                    assert.equal(iframeUrl.pathname, '/construct-item-preview/', 'Iframe uses item preview endpoint');
                    assert.equal(iframeUrl.searchParams.get('itemUri'), itemUri, 'itemUri is included');
                    assert.equal(iframeUrl.searchParams.get('readonly'), 'true', 'readonly is included');
                    assert.equal(iframeUrl.searchParams.get('postMessageTokens'), 'true', 'postMessageTokens is included');

                    iframe.contentWindow.postMessage = function (payload, targetOrigin) {
                        postMessages.push({ payload, targetOrigin });
                    };

                    dispatchMessage(iframe, { event: 'awaiting-tokens', parameters: {} });

                    assert.equal(postMessages.length, 1, 'Tokens are sent to the iframe');
                    assert.equal(postMessages[0].payload.event, 'tokens');
                    assert.equal(postMessages[0].payload.parameters.tokens.refreshToken, 'refresh-123');
                    assert.equal(postMessages[0].payload.parameters.tokens.accessToken, 'access-123');

                    dispatchMessage(iframe, { event: 'ready', parameters: {} });

                    assert.notOk(iframe.classList.contains('visually-hidden'), 'Iframe is visible after ready');
                    assert.equal(
                        $('#item-properties-preview .item-properties-preview-loading').length,
                        0,
                        'Loading state is removed after ready'
                    );
                    done();
                });
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('shows iframe error message as text and removes listener', function (assert) {
        const done = assert.async();
        const postMessages = [];

        loadModule()
            .then(({ preview }) => {
                preview.init(config);

                return flushAsync().then(() => {
                    const iframe = document.querySelector('.item-properties-preview-iframe');

                    iframe.contentWindow.postMessage = function (payload) {
                        postMessages.push(payload);
                    };

                    dispatchMessage(iframe, {
                        event: 'error',
                        parameters: {
                            error: {
                                message: '<img src=x onerror=alert(1)>'
                            }
                        }
                    });

                    const $error = $('#item-properties-preview .item-properties-preview-error');
                    assert.equal($error.length, 1, 'Error message is rendered');
                    assert.equal($error.text(), '<img src=x onerror=alert(1)>', 'Error message is rendered as text');
                    assert.equal($error.html(), '&lt;img src=x onerror=alert(1)&gt;', 'Error message is not injected as HTML');
                    assert.equal(document.querySelector('.item-properties-preview-iframe'), null, 'Iframe is removed on error');

                    postMessages.length = 0;
                    dispatchMessage(iframe, { event: 'awaiting-tokens', parameters: {} });
                    assert.equal(postMessages.length, 0, 'Message listener is removed after error');
                    done();
                });
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('cleanup removes iframe and message listener', function (assert) {
        const done = assert.async();
        let removeListenerCount = 0;
        const originalRemoveEventListener = window.removeEventListener;

        window.removeEventListener = function (type, listener, options) {
            if (type === 'message') {
                removeListenerCount += 1;
            }
            return originalRemoveEventListener.call(this, type, listener, options);
        };

        loadModule()
            .then(({ preview }) => {
                preview.init(config);

                return flushAsync().then(() => {
                    assert.ok(document.querySelector('.item-properties-preview-iframe'), 'Iframe exists before cleanup');

                    preview.cleanup();

                    assert.equal($('#item-properties-preview').children().length, 0, 'Preview container is cleared');
                    assert.ok(removeListenerCount >= 1, 'Message listener is removed on cleanup');
                    done();
                });
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            })
            .finally(() => {
                window.removeEventListener = originalRemoveEventListener;
            });
    });
});
