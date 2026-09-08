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
    // Keep in sync with taoItems/preview/inlinePropertiesPreview.js
    const readyTimeoutMs = 30000;
    const loadingDelayMs = 1500;
    const itemUri = 'item://test-item';

    const config = {
        isPreviewEnabled: true,
        itemUri
    };

    function setupDom() {
        $('#qunit-fixture').html(`
            <div id="item-properties-form-column" class="item-properties-column item-properties-column--form"></div>
            <div id="item-properties-preview-column" class="item-properties-column item-properties-column--preview">
                <div id="item-properties-preview" class="item-properties-preview"></div>
            </div>
        `);
    }

    function enableExternalPreviewer() {
        context.previewerExternalFeUrl = window.location.origin;
        context.featureFlags = context.featureFlags || {};
        context.featureFlags.FEATURE_FLAG_TAO_ADVANCE_EXTERNAL_ITEM_PREVIEWER = true;
        context.featureFlags.FEATURE_FLAG_TAO_CG_ONLY = false;
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

    function patchTimers(fastDelays, run) {
        const originalSetTimeout = window.setTimeout;
        const delays = Array.isArray(fastDelays) ? fastDelays : [fastDelays];

        window.setTimeout = function (handler, delay) {
            if (delays.includes(delay)) {
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

    function patchReadyTimeout(run) {
        return patchTimers(readyTimeoutMs, run);
    }

    function patchLoadingDelay(run) {
        return patchTimers(loadingDelayMs, run);
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
            context.featureFlags = Object.assign({}, context.featureFlags);
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
                    $('#item-properties-form-column').hasClass('item-properties-column--form'),
                    'Form column keeps its fixed width'
                );
                assert.notOk(
                    $('#item-properties-form-column').hasClass('item-properties-column--full'),
                    'Form column does not expand to full width'
                );
                done();
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('hides preview panel when FEATURE_FLAG_TAO_CG_ONLY is enabled', function (assert) {
        const done = assert.async();

        context.featureFlags.FEATURE_FLAG_TAO_CG_ONLY = true;

        loadModule()
            .then(({ preview, requestCalls }) => {
                preview.init(config);

                assert.ok($('#item-properties-preview-column').is(':hidden'), 'Preview column is hidden');
                assert.equal(requestCalls.length, 0, 'Token request is not made when CG-only is enabled');
                assert.equal(
                    document.querySelector('.item-properties-preview-iframe'),
                    null,
                    'Iframe is not mounted when CG-only is enabled'
                );
                done();
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('keeps external previewer available when FEATURE_FLAG_TAO_CG_ONLY is disabled', function (assert) {
        const done = assert.async();

        context.featureFlags.FEATURE_FLAG_TAO_CG_ONLY = false;

        loadModule()
            .then(({ preview, requestCalls }) => {
                preview.init(config);

                return flushAsync().then(() => {
                    assert.notOk(
                        $('#item-properties-preview-column').is(':hidden'),
                        'Preview column remains visible'
                    );
                    assert.equal(requestCalls.length, 1, 'Token request is still made');
                    assert.ok(
                        document.querySelector('.item-properties-preview-iframe'),
                        'Iframe is still mounted'
                    );
                    preview.cleanup();
                    done();
                });
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
                    assert.equal(
                        $('#item-properties-preview .item-properties-preview-loading').length,
                        0,
                        'Loading is not shown immediately after iframe mount'
                    );

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
                    preview.cleanup();
                    done();
                });
            })
            .catch(err => {
                assert.ok(false, err && err.message ? err.message : String(err));
                done();
            });
    });

    QUnit.test('shows loading text after delay while awaiting ready', function (assert) {
        const done = assert.async();

        patchLoadingDelay(() =>
            loadModule().then(({ preview }) => {
                preview.init(config);

                return flushAsync()
                    .then(() => {
                        assert.equal(
                            $('#item-properties-preview .item-properties-preview-loading').length,
                            0,
                            'Loading is not shown before the delay elapses'
                        );
                    })
                    .then(() => new Promise(resolve => setTimeout(resolve, 20)))
                    .then(() => {
                        assert.equal(
                            $('#item-properties-preview .item-properties-preview-loading').length,
                            1,
                            'Loading is shown after the delay while iframe awaits ready'
                        );

                        const iframe = document.querySelector('.item-properties-preview-iframe');
                        dispatchMessage(iframe, { event: 'ready', parameters: {} });

                        assert.equal(
                            $('#item-properties-preview .item-properties-preview-loading').length,
                            0,
                            'Loading is removed after ready'
                        );
                        preview.cleanup();
                        done();
                    });
            })
        ).catch(err => {
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
