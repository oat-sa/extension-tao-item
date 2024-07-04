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
define(['jquery', 'i18n', 'module', 'layout/actions', 'ui/lock', 'layout/section'], function (
    $,
    __,
    module,
    actions,
    lock,
    section
) {
    'use strict';

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
        }
    };

    return editItemController;
});
