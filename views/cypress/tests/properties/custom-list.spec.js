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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA ;
 */

import urls from '../../utils/urls';
import propertiesInfo from '../../utils/propertiesInfo';
import selectors from '../../utils/selectors';
import { getRandomNumber } from '../../../../../tao/views/cypress/utils/helpers';

/**
 * Creating list without exit editing
 */
const createList = () => {
    cy.intercept('POST', urlBO.list.index).as('createList');
    cy.getSettled(selectorsBO.createListButton)
        .should('have.text', ' Create list')
        .should('be.visible')
        .click();

    return cy.wait('@createList');
};

describe('Resource properties - Cycle through simple types', () => {
    const className = `Test E2E class ${getRandomNumber()}`;
    const childItemName = 'Test E2E child item';
    const childClassName = 'Test E2E child class';
    const options = {
        nodeName: selectors.root,
        className: className,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit,
        propertyListValue: 'Magic list',
    };

    /**
     * Log in and wait for render
     * After @treeRender click root class
     */
    before(() => {
        // cy.log('COMMAND: addTree');
        // cy.loginAsAdmin();
        // cy.intercept('GET', '**/taoBackOffice/Lists/index').as('getLists')
        // cy.visit('/tao/Main/index?structure=settings&ext=tao&section=taoBo_list');
        // cy.wait('@getLists');

        cy.setup(
            selectors.treeRenderUrl,
            selectors.editClassLabelUrl,
            urls.items,
            selectors.root
        );
    });

    after(() => {
        // cy.deleteClassFromRoot(
        //     selectors.root,
        //     selectors.itemClassForm,
        //     selectors.deleteClass,
        //     selectors.deleteConfirm,
        //     className,
        //     selectors.deleteClassUrl,
        //     true
        // );
    });

     /**
      * Tests
      */
     describe('Main Item Class creation and editing', () => {
        before(() => {
            cy.addClassToRoot(
                selectors.root,
                selectors.itemClassForm,
                className,
                selectors.editClassLabelUrl,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            );
        });

        // it('can edit and add new "List Custom - Single Choice - Radio Button" type property to the item class', function () {
        //     options.propertyName = propertiesInfo.list.name;
        //     options.propertyType = propertiesInfo.list.type;

        //     cy.addPropertyToClass(options);
        // });

        // it('can edit and add new "List Custom - Single Choice - Drop Down" type property to the item class', function () {
        //     options.propertyName = propertiesInfo.longList.name;
        //     options.propertyType = propertiesInfo.longList.type;

        //     cy.addPropertyToClass(options);
        // });

        // it('can edit and add new "List Custom - Multiple Choice - Check box" type property to the item class', function () {
        //     options.propertyName = propertiesInfo.multiList.name;
        //     options.propertyType = propertiesInfo.multiList.type;

        //     cy.addPropertyToClass(options);
        // });

        // it('can edit and add new "List Custom - Multiple Choice - Check box" type property to the item class', function () {
        //     options.propertyName = propertiesInfo.multiSearchList.name;
        //     options.propertyType = propertiesInfo.multiSearchList.type;

        //     cy.addPropertyToClass(options);
        // });

        // it('can edit and add new "List Custom - Single Choice - Search Input" type property to the item class', function () {
        //     options.propertyName = propertiesInfo.singleSearchList.name;
        //     options.propertyType = propertiesInfo.singleSearchList.type;

        //     cy.addPropertyToClass(options);
        // });

        describe('Child Item Class', () => {
            before(() => {
                // Create new child item class
                cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel');
                cy.addClass(
                    selectors.itemClassForm,
                    selectors.treeRenderUrl,
                    selectors.addSubClassUrl
                );
                cy.renameSelectedClass(selectors.itemClassForm, childClassName);

                // Edit class
                cy.intercept('POST', `**/${ selectors.editClassUrl }`).as('editClass');
                cy.getSettled(selectors.editClass).click();
                cy.wait('@editClass');
            });

            it('Inherits "List Custom - Single Choice - Radio Button" type property', function() {
                const property = propertiesInfo.list;
                property.listValue = selectors.customListValue;

                cy.validateClassProperty(options, property);
            });
        });

        describe('Child Item', () => {
            before(() => {
                cy.selectNode(selectors.root, selectors.itemClassForm, className);
                cy.addNode(selectors.itemForm, selectors.addItem);
                cy.renameSelectedNode(selectors.itemForm, selectors.editItemUrl, childItemName);

                // Edit item
                cy.selectNode(selectors.root, selectors.itemClassForm, className);
                cy.intercept('POST', `**${selectors.editItemUrl}`).as('editItem');
                cy.getSettled(`li [title ="${childItemName}"] a`).last().click();
                cy.wait('@editItem');
            });

            it('child item inherits parent property "List Custom - Single Choice - Radio Button" and sets value', function () {
                const property = propertiesInfo.list;
                const value = selectors.customListValue;

                cy.assignValueToSelectProperty(property, value);
            });

            // it('can save update of child item properties', function () {
            //     cy.intercept('POST', `**${selectors.editItemUrl}`).as('editItem');
            //     cy.get('.form-toolbar button[data-testid="save"]').click();
            //     cy.wait('@editItem').then(xhr => {
            //         expect(xhr.response.statusCode).to.eq(200);
            //     });
            // });
        });
    });
});
