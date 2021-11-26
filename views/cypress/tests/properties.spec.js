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
 import propertiesInfo from '../utils/propertiesInfo';
 import selectors from '../utils/selectors';
 import { getRandomNumber } from '../../../../tao/views/cypress/utils/helpers';

 describe('Resource properties - Cycle through simple types', () => {
    const className = `Test E2E class 883`;
    const newPropertyName = 'I am a new property in testing, hi!';
    const childItemName = 'Test E2E child item';
    const childClassName = 'Test E2E child class';
    const options = {
        nodeName: selectors.root,
        className: className,
        propertyName: newPropertyName,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit,
        propertyListValue: 'Boolean',
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

    // after(() => {
    //     cy.deleteClassFromRoot(
    //         selectors.root,
    //         selectors.itemClassForm,
    //         selectors.deleteClass,
    //         selectors.deleteConfirm,
    //         className,
    //         selectors.deleteClassUrl,
    //         true
    //     );
    // });

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

        it('can edit and add new "Text - Short - Field" type property to the item class', function () {
            options.propertyName = propertiesInfo.text.name;
            options.propertyType = propertiesInfo.text.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "Text - Long - Box" type property to the item class', function () {
            options.propertyName = propertiesInfo.longText.name;
            options.propertyType = propertiesInfo.longText.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "Text - Long - HTML Editor" type property to the item class', function () {
            options.propertyName = propertiesInfo.html.name;
            options.propertyType = propertiesInfo.html.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "List - Single Choice - Radio Button" type property to the item class', function () {
            options.propertyName = propertiesInfo.list.name;
            options.propertyType = propertiesInfo.list.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "List - Single Choice - Drop Down" type property to the item class', function () {
            options.propertyName = propertiesInfo.longList.name;
            options.propertyType = propertiesInfo.longList.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "List - Multiple Choice - Checkbox" type property to the item class', function () {
            options.propertyName = propertiesInfo.multiList.name;
            options.propertyType = propertiesInfo.multiList.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "List - Multiple Choice - Search Input" type property to the item class', function () {
            options.propertyName = propertiesInfo.multiSearchList.name;
            options.propertyType = propertiesInfo.multiSearchList.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "List - Single Choice - Search Input" type property to the item class', function () {
            options.propertyName = propertiesInfo.singleSearchList.name;
            options.propertyType = propertiesInfo.singleSearchList.type;
            cy.addPropertyToClass(options);
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
    });

    describe.only('Child Item Class inherits parent properties correctly', () => {

        it('Edit class', function() {
            cy.get('#https_2_bosa_0_docker_0_localhost_1_ontologies_1_tao_0_rdf_3_i619f59bb107e284cab43c7fb717177d > a').click();
            cy.get('#https_2_bosa_0_docker_0_localhost_1_ontologies_1_tao_0_rdf_3_i619f5a0905f758898d8b893d0994c64 > a').click();
            cy.intercept('POST', `**/${ selectors.editClassUrl }`).as('editClass');
            cy.getSettled(selectors.editClass).click();
            cy.wait('@editClass');
        });

        it('Inherits "Text - Short - Field" type property', function() {
            const property = propertiesInfo.text;

            // TODO: move this to a command
            // make list value optional
            cy.getSettled(selectors.classOptions)
              .contains('.property-heading-label', property.name)
              .siblings('.property-heading-toolbar')
              .contains(className)
              .within(() => {
                cy.get('.icon-edit').click();
              });
            cy.getSettled('.property-edit-container-open [data-testid="Label"]').should('have.value', property.name);
            cy.getSettled('.property-edit-container-open [data-testid="Type"]').should('have.value', property.type);
            cy.getSettled('.property-edit-container-open .icon-edit').click();
            // cy.getSettled('.property-edit-container [data-testid="List values"]').should('have.value', selectors.booleanListValue);
        });
    });

    describe('Child Item', () => {
        it('create a new child item', function () {
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.get(`input[data-testid="${ newPropertyName }"]`).eq(0).check({ force: true });
            cy.renameSelectedNode(selectors.itemForm, selectors.editItemUrl, childItemName);
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
