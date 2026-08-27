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
 * Copyright (c) 2014-2024 (original work) Open Assessment Technologies SA;
 */
define([
    'jquery',
    'i18n',
    'module',
    'layout/actions',
    'ui/feedback',
    'ui/lock',
    'layout/section',
    'util/url',
    'uri',
    'taoItems/provider/category',
    'taoItems/preview/inlinePropertiesPreview'
], function ($, __, module, actions, feedback, lock, section, urlUtil, uriUtil, categoryProviderFactory, inlinePropertiesPreview) {
    'use strict';

    var categoryProvider = categoryProviderFactory();

    function displayInvalidAutomaticCategoryValues(invalidValues) {
        const $form = $('.content-block .xhtml_form:first form');
        let hasInvalidValues = false;

        if (!$form.length) {
            return;
        }

        $form.prev('.invalid-automatic-category-message').remove();
        Object.keys(invalidValues).forEach(propertyUri => {
            const invalidProperty = invalidValues[propertyUri];
            const encodedPropertyUri = uriUtil.encode(propertyUri);
            const $input = $form.find(
                `[id="property_${encodedPropertyUri}"], [id="${encodedPropertyUri}"], [name="${encodedPropertyUri}"]`
            ).first();
            let $property = $input.closest('.form-group');

            if (!$property.length && $input.length) {
                $property = $input.parent();
            }

            if (!$property.length) {
                const $label = $form.find('label').filter(function () {
                    return $(this).text().replace('*', '').trim() === invalidProperty.label;
                }).first();
                $property = $label.closest('.form-group');
                if (!$property.length) {
                    $property = $label.parent();
                }
            }

            if (!invalidProperty.values.length) {
                return;
            }

            hasInvalidValues = true;
            $property.find('.invalid-automatic-category-message').remove();
            $('<p>', {
                class: 'invalid-automatic-category-message feedback-warning',
                text: __('The value "%s" cannot be used as an automatic test category.', invalidProperty.values.join('", "'))
            }).appendTo($property);
        });

        if (hasInvalidValues && !$form.find('.invalid-automatic-category-message').length) {
            $('<p>', {
                class: 'invalid-automatic-category-message feedback-warning',
                text: __('One or more values cannot be used as automatic test categories.')
            }).insertBefore($form);
        }
    }

    function handleInvalidAutomaticCategoriesError() {
        feedback().error(__('Unable to validate automatic test categories.'));
    }

    /**
     * The item properties controller
     */
    var editItemController = {
        /**
         * Controller entry point
         */
        start() {
            const config = module.config();
            const maxButtons = 10; // arbitrary value for the max number of buttons

            const getPreviewId = idx => `item-preview${idx ? `-${idx}` : ''}`;
            const previewActions = [];
            for (let i = 0; i < maxButtons; i++) {
                const action = actions.getBy(getPreviewId(i));
                if (!action) {
                    break;
                }
                previewActions.push(action);
            }
            previewActions.forEach(previewAction => {
                previewAction.state.disabled = !config.isPreviewEnabled;
            });

            const authoringAction = actions.getBy('item-authoring');
            if (authoringAction) {
                authoringAction.state.disabled = !config.isAuthoringEnabled;
            }
            actions.updateState();

            $('#lock-box').each(function () {
                lock($(this)).register();
            });

            //some of the others sections (like the authoring) might have an impact
            //on the state of the other actions, so we reload when we come back
            section.off('show').on('show', sectionContext => {
                if (sectionContext.id === 'manage_items') {
                    actions.exec('item-properties');
                }
            });

            // Auto-trigger action if specified in URL (e.g., from test creator's Edit button)
            const parsedUrl = urlUtil.parse(window.location.href);
            const autoAction = parsedUrl.query.autoAction;
            if (autoAction && config.isAuthoringEnabled && actions.getBy(autoAction)) {
                actions.exec(autoAction);
            }

            if (config.itemUri) {
                categoryProvider
                    .getInvalidExposedCategories(config.itemUri)
                    .then(displayInvalidAutomaticCategoryValues)
                    .catch(handleInvalidAutomaticCategoriesError);
            }

            if (config.isPreviewEnabled && config.itemUri) {
                inlinePropertiesPreview.init(config);
            }
        }
    };

    return editItemController;
});
