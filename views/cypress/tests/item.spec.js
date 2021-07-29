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
    const newPropertyName = 'I am a new property in testing, hi!';

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
            cy.addClassToRoot(selectors.root, selectors.itemClassForm, className);
        });
        it('can create and edit a new property for the class', function () {
            cy.addPropertyToClass(
                selectors.newClass,
                selectors.editClass,
                selectors.classOptions,
                newPropertyName,
                selectors.propertyEdit
            );
        });

        it('can create and rename a new item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.renameSelected(selectors.itemForm, 'Test E2E item 1');
        });

        it('can delete item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.renameSelected(selectors.itemForm, 'Test E2E item 2');
            cy.deleteNode(selectors.deleteItem, 'Test E2E item 2');
        });

        it('can delete item class', function () {
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.itemClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className
            );
        });

    });
});
