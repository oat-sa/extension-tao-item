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


describe('Items', () => {
    const newClassName = 'Test E2E class';
    const newItemName = 'Test E2E item';

    const selectors = {
        deleteItem: '[data-context="instance"][data-action="deleteItem"]',
        deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
        addItem: '[data-context="resource"][data-action="instanciate"]',
        itemForm: 'form[action="/taoItems/Items/editItem"]',
        itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',
        deleteConfirm: '[data-control="delete"]',
        root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]',
        nodeWithName: name => `li[title="${name}"] a`,
    }

    const itemsUrl = '/tao/Main/index?structure=items&ext=taoItems&section=manage_items';

    /**
     * Log in
     * Visit the page
     */
    beforeEach(() => {
        cy.loginAsAdmin();

        cy.visit(itemsUrl);

        cy.get(selectors.root).then(root => {
            if (root.find(selectors.nodeWithName(newClassName)).length === 0) {
                cy.addClass(selectors.itemClassForm);
                cy.renameSelected(selectors.itemClassForm, newClassName);
            }
        });
    });

    /**
     * Delete newly created items after each step
     */
    afterEach(() => {
        cy.get(selectors.root).then(root => {
            if (root.find(selectors.nodeWithName(newClassName)).length > 0) {
                cy.deleteClass(selectors.itemClassForm, selectors.deleteClass, selectors.deleteConfirm, newClassName);
            }
        });
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
