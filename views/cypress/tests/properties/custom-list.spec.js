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
import urlsTAO from '../../../../../tao/views/cypress/utils/urls';
import propertiesInfo from '../../utils/propertiesInfo';
import selectors from '../../utils/selectors';
import selectorsList from '../../../../../tao/views/cypress/utils/selectors/list';
import { getRandomNumber } from '../../../../../tao/views/cypress/utils/helpers';

const LIST_NAME_PREFIX = 'Test E2E list';

describe('Resource properties - Cycle through simple types', () => {
    const className = `Test E2E class ${getRandomNumber()}`;
    const childClassName = 'Test E2E child class';
    const listName = `${LIST_NAME_PREFIX}_${getRandomNumber()}`;
    const options = {
        nodeName: selectors.root,
        className: className,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit,
        propertyListValue: listName,
    };
    const testData = [{
            title: 'Single Choice - Radio Button',
            props: propertiesInfo.list,
        },{
            title: 'Single Choice - Drop Down',
            props: propertiesInfo.longList,
        },{
            title: 'Multiple Choice - Check box',
            props: propertiesInfo.multiList,
        },{
            title: 'Multiple Choice - Check box',
            props: propertiesInfo.multiSearchList,
        },{
            title: 'Single Choice - Search Input',
            props: propertiesInfo.singleSearchList,
        }];
    let listURI;

    /**
     * Validate list elements
     */
    const validateList = () => {
        cy.getSettled('.property-edit-container-open .form-elt-list li').contains('Element 1').should('have.length', 1);
        cy.getSettled('.property-edit-container-open .form-elt-list li').contains('Element 2').should('have.length', 1);
    };

    /**
     * Click edit on property (name) in manage schema
     * @param {String} name - property name to edit
     */
    const editProperty = (name) => {
        cy.getSettled(options.classOptions)
            .contains('.property-heading-label', name)
            .siblings('.property-heading-toolbar')
            .within(() => {
                cy.get('.icon-edit').click();
            });
    };

    /**
     * Log in and wait for render
     * After @treeRender click root class
     */
    before(() => {
        cy.loginAsAdmin();

        // Create a list
        cy.intercept('GET', '**/taoBackOffice/Lists/index').as('getLists')
        cy.visit(urlsTAO.settings.list);
        cy.wait('@getLists');

        cy.createList()
            .then((interception)=>{
                listURI = interception.response.body.data.uri.split('#').pop();

                // Add extra element to the list
                cy.getSettled(`section[id$="${listURI}"]`)
                    .find(selectorsList.addElementButton)
                    .should('be.visible')
                    .click();

                cy.getSettled(`section[id$="${listURI}"]`)
                    .find(selectorsList.elementsList)
                    .find('li:last-child')
                    .find(selectorsList.elementNameInput)
                    .should('be.visible')
                    .type(`Element 2`);

                cy.saveList(listName);
            });

        // Go to items
        cy.setup(
            selectors.treeRenderUrl,
            selectors.editClassLabelUrl,
            urls.items,
            selectors.root
        );
    });

    after(() => {
        // Delete created class
        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            className,
            selectors.deleteClassUrl,
            true
        );

        // Delete created list
        cy.intercept('GET', '**/taoBackOffice/Lists/index').as('getLists')
        cy.visit(urlsTAO.settings.list);
        cy.wait('@getLists');

        cy.deleteList(listURI);
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

        describe('Can add custom list to parent class', () => {
            testData.forEach((testcase, index) => {
                it(`${index}: "List Custom - ${testcase.title}" type property`, function () {
                    options.propertyName = testcase.props.name;
                    options.propertyType = testcase.props.type;

                    cy.addPropertyToClass(options);
                });
            });
        });

        describe('Restore type when edit parent class', () => {
            testData.forEach((testcase, index) => {
                // Enable when bug will be fixed
                it.skip(`${index}: "List Custom - ${testcase.title}" type property`, function () {
                    editProperty(testcase.props.name);
                    validateList();
                    cy.getSettled('.property-edit-container-open .icon-edit').click();
                });
            });
        });

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

            testData.forEach((testcase, index) => {
                it(`${index}: Inherits "${testcase.title}" type property`, function () {
                    editProperty(testcase.props.name);
                    validateList();
                    cy.getSettled('.property-edit-container-open .icon-edit').click();
                });
            });
        });
    });
});
