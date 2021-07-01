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
    const newClassName = 'Test E2E class';
    const newItemName = 'Test E2E item';

    /**
     * Log in
     * Visit the page
     */
    beforeEach(() => {
        cy.loginAsAdmin();

        cy.visit(urls.items);

        cy.addClassToRoot(selectors.root, selectors.itemClassForm, newClassName);
    });

    /**
     * Delete newly created items after each step
     */
    afterEach(() => {
        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            newClassName
        );
    });

    /**
     * Tests
     */
    describe('Item creation, editing and deletion', () => {
        it('can create and rename a new item', function () {
            cy.selectNode(selectors.itemClassForm, newClassName);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.renameSelected(selectors.itemForm, newItemName);
        });

        it('can delete item', function () {
            cy.selectNode(selectors.itemClassForm, newClassName);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.renameSelected(selectors.itemForm, newItemName);
            cy.deleteNode(selectors.deleteItem, newItemName);
        });
    });
});
