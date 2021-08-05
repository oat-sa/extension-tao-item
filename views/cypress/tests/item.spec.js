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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA ;
 */

import urls from '../utils/urls';
import selectors from '../utils/selectors';

describe('Items', () => {
    const className = 'Test E2E class';
    const classMovedName = 'Test E2E class Moved';

    /**
     * Visit the page
     */
    beforeEach(() => {
        cy.visit(urls.items);
    });

    /**
     * Log in
     */
    before(() => {
        cy.loginAsAdmin();
    });

    /**
     * Tests
     */
    describe('Item creation, editing and deletion', () => {
        it('can create a new item class', function () {
            cy.addClassToRoot(
                selectors.root,
                selectors.itemClassForm,
                className,
                selectors.editClassLabelUrl,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            );
        });

        it('can create and rename a new item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className)
                .addNode(selectors.itemForm, selectors.addItem)
                .renameSelected(selectors.itemForm, 'Test E2E item 1');
        });

        it('can delete item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className)
                .addNode(selectors.itemForm, selectors.addItem)
                .renameSelected(selectors.itemForm, 'Test E2E item 2')
                .deleteNode(
                    selectors.root,
                    selectors.deleteItem,
                    'Test E2E item 2',
                    selectors.treeRenderUrl,
                    selectors.editItem,
                    false,
                    true
                );
        });

        it('can delete item class', function () {
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.itemClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className,
                selectors.treeRenderUrl,
                selectors.resourceRelations,
                false,
                true
            );
        });

        it('can delete empty item class', function () {
            cy.addClassToRoot(
                selectors.root,
                selectors.itemClassForm,
                className,
                selectors.editClassLabelUrl,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            )
                .deleteClassFromRoot(
                    selectors.root,
                    selectors.itemClassForm,
                    selectors.deleteClass,
                    selectors.deleteConfirm,
                    className,
                    selectors.treeRenderUrl,
                    selectors.resourceRelations,
                    false,
                    true
                );
        });

        it('can move item class', function () {
            cy.moveClassFromRoot(
                selectors.root,
                selectors.itemClassForm,
                selectors.moveClass,
                selectors.moveConfirmSelector,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className,
                classMovedName,
                selectors.treeRenderUrl,
                selectors.editClassLabelUrl,
                selectors.restResourceGetAll,
                selectors.resourceRelations,
                selectors.addSubClassUrl
            );
        });
    });
});
