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
 import { getRandomNumber } from '../../../../tao/views/cypress/utils/helpers';

 describe('Manage Schema', () => {
    const className = `Test E2E class ${getRandomNumber()}`;
    const newPropertyName = 'I am a new property in testing, hi!';
     const secondClassText = 'secondClassInOrder';
    const childItemName = 'Test E2E child item';
    const childClassName = 'Test E2E child class';
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

    after(() => {
        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            className,
            selectors.deleteClassUrl,
            true
        );
    });

    /**
     * Tests
     */
    describe('Main Item Class creation and editing', () => {
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

        it('can edit and add new property for the item class', function () {
            cy.addPropertyToClass(options);
            const optionsToFindInput = {
                input: 'input',
                position: 4,
                type: 'text',
                editClassSelector: selectors.editClassUrl,
                propertyEdit: selectors.propertyEdit,
                newValue: 15
            };
            cy.findInputInManageSchema(optionsToFindInput);
        });

        it('validate restriction - notEmpty', function () {
            const options = {
                input: 'input[type="checkbox"]',
                type: 'checkbox',
                editClassSelector: selectors.editClassUrl,
                propertyEdit: selectors.propertyEdit,
            };
            cy.findInputInManageSchema(options);
        });

        it('validate restriction - languageDependant', function () {
            const options = {
                input: 'input[type="radio"]',
                position: 1,
                type: 'radio',
                editClassSelector: selectors.editClassUrl,
                propertyEdit: selectors.propertyEdit,
            };
            cy.findInputInManageSchema(options);
        });

        it('validate restriction - formFieldOrder', function () {
            const optionsToAddProperty = {
                nodeName: selectors.root,
                className: className,
                propertyName: secondClassText,
                propertyAlias: 'secondClassInOrderAlias',
                nodePropertiesForm: selectors.itemClassForm,
                manageSchemaSelector: selectors.editClass,
                classOptions: selectors.classOptions,
                editUrl: selectors.editClassUrl,
                propertyEditSelector: selectors.propertyEdit
            }
            cy.addPropertyToClass(optionsToAddProperty);
            const optionsToFindInput = {
                input: 'input',
                position: 4,
                type: 'text',
                editClassSelector: selectors.editClassUrl,
                propertyEdit: selectors.propertyEdit,
                newValue: 25
            };
            cy.findInputInManageSchema(optionsToFindInput);
        });
    });

    describe('Child Item Class', () => {
        it('create a new child item class', function () {
            cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel');
            cy.addClass(
                selectors.itemClassForm,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            );
            cy.renameSelectedClass(selectors.itemClassForm, childClassName);
        });

        it('child item class inherits parent property correctly', function() {
            cy.intercept('POST', `**/${ selectors.editClassUrl }`).as('editClass');
            cy.getSettled(selectors.editClass).click();
            cy.wait('@editClass');
            cy.getSettled(selectors.classOptions)
              .contains('.property-block', newPropertyName)
              .contains('.property-heading-toolbar', className)
              .within(() => {
                cy.get('.icon-edit').click();
              });
            cy.getSettled('.property-edit-container-open [data-testid="Label"]').should('have.value', newPropertyName);
            cy.getSettled('.property-edit-container-open [data-testid="Alias"]').should('have.value', newPropertyAlias);
            cy.getSettled('.property-edit-container-open [data-testid="Type"]').should('have.value', 'list');
            cy.getSettled('.property-edit-container-open [data-testid="List values"]').should('have.value', selectors.booleanListValue);
        });
    });

    describe('Child Item', () => {
        it('create a new child item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.get(`input[data-testid="${ newPropertyName }"]`).eq(0).check({ force: true });
            cy.renameSelectedNode(selectors.itemForm, selectors.editItemUrl, childItemName);
        });

        it('validate form field order in child item', function () {
            cy.get('form .form_desc').eq(3).should('have.text', newPropertyName + '*');
            cy.get('form .form_desc').eq(4).should('have.text', secondClassText);
        });

        it('appears error on save due to notEmpty restriction', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.intercept('POST', `**${selectors.editItemUrl}`).as('edit')
                .get('button[id="Save"]')
                .click()
                .wait('@edit')
            cy.get('div[class="form-error"]').should('have.text', 'This field is required');
            cy.get(`input[data-testid="${ newPropertyName }"]`).eq(0).check({ force: true });
        });


        it('child item inherits parent property and sets value', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.assignValueToProperty(childItemName, selectors.itemForm, `[data-testid="${newPropertyName}"]`, selectors.treeRenderUrl, selectors.editItemUrl);
        });
    });

    describe('Delete property', () => {
        it('Remove property from main item class', function() {
            cy.removePropertyFromClass(options);
        });

        it('Check removed property is not present in child item anymore', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.intercept('POST', selectors.editItemUrl).as('editItem');
            cy.getSettled(`li [title ="${childItemName}"] a`).last().click();
            cy.wait('@editItem');
            cy.get(`[data-testid="${newPropertyName}"]`).should('not.exist');
        });
    });
});
