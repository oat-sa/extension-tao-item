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
    const newPropertyName = 'I am a new property in testing, hi!';
    const itemName = 'Test E2E item 1';
    const newPropertyAlias = 'testing_property_alias';
    const options = {
        nodeName: selectors.root,
        className: className,
        propertyName: newPropertyName,
        propertyAlias: newPropertyAlias,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit
    };

    /**
     * Log in and wait for render
     * After @treeRender click root class
     */
    before(() => {
        cy.setup(
            selectors.treeRenderUrl,
            selectors.editClassLabelUrl,
            urls.items,
            selectors.root
        );
    });

    /**
     * Tests
     */
    describe('Item Class creation and editing', () => {
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

        it('can edit and add new property for the class', function () {
            cy.addPropertyToClass(options);
        });
    });

    describe('Item creation and edition', () => {
        it('can create and rename a new item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className)
                .addNode(selectors.itemForm, selectors.addItem)
                .renameSelectedNode(selectors.itemForm, selectors.editItemUrl, 'Test E2E item 1');
        });

        it('can give a property value to an item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.assignValueToProperty(itemName, selectors.itemForm, `[data-testid="${newPropertyName}"]`, selectors.treeRenderUrl, selectors.editItemUrl);
        });
    });

    describe('Moving and deleting', () => {
        it('can delete item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className)
                .addNode(selectors.itemForm, selectors.addItem)
                .renameSelectedNode(selectors.itemForm, selectors.editItemUrl, 'Test E2E item 2')
                .deleteNode(
                    selectors.root,
                    selectors.deleteItem,
                    selectors.editItemUrl,
                    'Test E2E item 2',
                );
        });

        it('can move item class', function () {
            cy.intercept('POST', `**/${selectors.editClassLabelUrl}`).as('editClassLabel');

            cy.getSettled(`${selectors.root} a:nth(0)`)
                .click()
                .wait('@editClassLabel')
                .addClass(selectors.itemClassForm, selectors.treeRenderUrl, selectors.addSubClassUrl)
                .renameSelectedClass(selectors.itemClassForm, classMovedName);

            cy.wait('@treeRender');

            cy.moveClassFromRoot(
                selectors.root,
                selectors.moveClass,
                selectors.moveConfirmSelector,
                className,
                classMovedName,
                selectors.restResourceGetAll
            );
        });

        it('can delete item class', function () {
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.itemClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                classMovedName,
                selectors.deleteClassUrl,
                true
            );
        });

        it('can delete empty item class', function () {
            cy.intercept('POST', `**/${selectors.editClassLabelUrl}`).as('editClassLabel')
            cy.getSettled(`${selectors.root} a:nth(0)`)
                .click()
                .wait('@editClassLabel')
                .addClass(selectors.itemClassForm, selectors.treeRenderUrl, selectors.addSubClassUrl)
                .renameSelectedClass(selectors.itemClassForm, className);

            cy.wait('@editClassLabel');

            cy.deleteClassFromRoot(
                selectors.root,
                selectors.itemClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className,
                selectors.deleteClassUrl,
                selectors.resourceRelations,
                false,
                true
            );
        });
    });
});
