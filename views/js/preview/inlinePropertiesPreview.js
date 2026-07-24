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
define([
    'context',
    'jquery',
    'i18n',
    'core/request',
    'util/url',
    'css!taoItems/preview/css/inlinePropertiesPreview.css'
], function (context, $, __, request, urlUtil) {
    'use strict';

    const readyTimeoutMs = 30000;
    const loadingDelayMs = 1500;

    let externalPreviewerOrigin;
    let iframe;
    let messageHandler;
    let readyTimeout;
    let loadingDelayTimeout;
    let tokens;
    let $container;

    /**
     * @returns {boolean}
     */
    function isExternalPreviewerAvailable() {
        return !!(
            context &&
            context.previewerExternalFeUrl &&
            context.featureFlags &&
            context.featureFlags.FEATURE_FLAG_TAO_ADVANCE_EXTERNAL_ITEM_PREVIEWER
        );
    }

    /**
     * @returns {Promise<{refreshToken: string, accessToken: string}>}
     */
    function getTokens() {
        return request({
            url: urlUtil.route('getTokens', 'Previewer', 'taoQtiTestPreviewer'),
            noToken: true
        }).then(data => {
            if (data && data.success) {
                return {
                    refreshToken: data.data.refresh_token,
                    accessToken: data.data.access_token
                };
            }

            throw new Error(__('Failed to get tokens for external previewer'));
        });
    }

    /**
     * @param {Object} payload
     */
    function notifyIframe(payload) {
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(payload, externalPreviewerOrigin);
        }
    }

    /**
     * @param {string} message
     */
    function showError(message) {
        if ($container && $container.length) {
            $container.empty().append($('<p>', { class: 'item-properties-preview-error', text: message }));
        }
    }

    function showLoading() {
        if ($container && $container.length && !$container.find('.item-properties-preview-loading').length) {
            $container.append($('<p>', { class: 'item-properties-preview-loading', text: __('Loading preview...') }));
        }
    }

    function clearLoadingDelay() {
        if (loadingDelayTimeout) {
            clearTimeout(loadingDelayTimeout);
            loadingDelayTimeout = null;
        }
    }

    function scheduleLoading() {
        clearLoadingDelay();
        loadingDelayTimeout = setTimeout(() => {
            loadingDelayTimeout = null;
            if (iframe && iframe.classList.contains('visually-hidden')) {
                showLoading();
            }
        }, loadingDelayMs);
    }

    function hideLoading() {
        clearLoadingDelay();
        if ($container && $container.length) {
            $container.find('.item-properties-preview-loading').remove();
        }
    }

    function hidePanel() {
        $('#item-properties-preview-column').hide();
        $('#item-properties-form-column')
            .removeClass('item-properties-column--form')
            .addClass('item-properties-column--full');
    }

    function showPanel() {
        $('#item-properties-preview-column').show();
        $('#item-properties-form-column')
            .removeClass('item-properties-column--full')
            .addClass('item-properties-column--form');
    }

    function handleTimeout() {
        readyTimeout = null;
        hideLoading();

        if (messageHandler) {
            window.removeEventListener('message', messageHandler);
            messageHandler = null;
        }

        iframe = null;
        tokens = null;
        showError(__('External previewer load timeout'));
    }

    /**
     * @param {MessageEvent} evt
     */
    function onMessage(evt) {
        const { origin, source, data } = evt;

        if (origin !== externalPreviewerOrigin || source !== iframe.contentWindow) {
            return;
        }

        switch (data.event) {
            case 'awaiting-tokens':
                notifyIframe({ event: 'tokens', parameters: { tokens } });
                break;

            case 'ready':
                hideLoading();
                if (readyTimeout) {
                    clearTimeout(readyTimeout);
                    readyTimeout = null;
                }
                if (iframe) {
                    iframe.classList.remove('visually-hidden');
                }
                break;

            case 'error': {
                hideLoading();
                if (messageHandler) {
                    window.removeEventListener('message', messageHandler);
                    messageHandler = null;
                }
                iframe = null;
                tokens = null;
                if (readyTimeout) {
                    clearTimeout(readyTimeout);
                    readyTimeout = null;
                }

                const errorMessage =
                    data.parameters &&
                    data.parameters.error &&
                    data.parameters.error.message ?
                        data.parameters.error.message :
                        __('Unable to load item preview');
                showError(errorMessage);
                break;
            }

            default:
                break;
        }
    }

    /**
     * @param {string} itemUri
     * @returns {string}
     */
    function buildIframeUrl(itemUri) {
        const url = new URL(`${externalPreviewerOrigin}/construct-item-preview/`);

        url.searchParams.append('itemUri', itemUri);
        url.searchParams.append('locale', (context && context.locale) || 'en-US');
        url.searchParams.append('previewerBeUrl', `${window.location.origin}/taoStudio`);
        url.searchParams.append('postMessageTokens', true);
        url.searchParams.append('readonly', true);

        return url.toString();
    }

    function cleanup() {
        if (readyTimeout) {
            clearTimeout(readyTimeout);
            readyTimeout = null;
        }

        hideLoading();

        if (messageHandler) {
            window.removeEventListener('message', messageHandler);
            messageHandler = null;
        }

        if ($container && $container.length) {
            $container.empty();
        }

        iframe = null;
        tokens = null;
    }

    /**
     * @param {Object} config
     * @param {boolean} config.isPreviewEnabled
     * @param {string} config.itemUri
     */
    function init(config) {
        cleanup();

        if (!config || !config.isPreviewEnabled || !config.itemUri) {
            return;
        }

        $container = $('#item-properties-preview');
        if (!$container.length) {
            return;
        }

        if (!isExternalPreviewerAvailable()) {
            hidePanel();
            return;
        }

        try {
            externalPreviewerOrigin = new URL(context.previewerExternalFeUrl).origin;
        } catch (err) {
            hidePanel();
            return;
        }

        showPanel();

        getTokens()
            .then(authTokens => {
                tokens = authTokens;

                iframe = document.createElement('iframe');
                iframe.className = 'item-properties-preview-iframe visually-hidden';
                iframe.setAttribute('title', __('Item preview'));
                iframe.setAttribute('frameborder', '0');
                iframe.setAttribute('allowfullscreen', 'true');

                $container.empty().append(iframe);
                scheduleLoading();

                messageHandler = onMessage;
                window.addEventListener('message', messageHandler);

                iframe.src = buildIframeUrl(config.itemUri);

                readyTimeout = setTimeout(handleTimeout, readyTimeoutMs);
            })
            .catch(() => {
                showError(__('Unable to load item preview'));
            });
    }

    return {
        init,
        cleanup
    };
});
