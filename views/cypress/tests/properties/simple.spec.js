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
        before(() => {
            // Create a new item class
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

        it('can edit and add new "Calendar" type property to the item class', function () {
            options.propertyName = propertiesInfo.calendar.name;
            options.propertyType = propertiesInfo.calendar.type;
            cy.addPropertyToClass(options);
        });

        it('can edit and add new "Password" type property to the item class', function () {
            options.propertyName = propertiesInfo.password.name;
            options.propertyType = propertiesInfo.password.type;
            cy.addPropertyToClass(options);
        });
    });

    describe('Child Item Class', () => {
        before(() => {
            // Create a new child item class
            cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel');
            cy.addClass(
                selectors.itemClassForm,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            );
            cy.renameSelectedClass(selectors.itemClassForm, childClassName);

            // Go to edit class
            cy.intercept('POST', `**/${ selectors.editClassUrl }`).as('editClass');
            cy.getSettled(selectors.editClass).click();
            cy.wait('@editClass');
        });

        it('Inherits "Text - Short - Field" type property', function() {
            const property = propertiesInfo.text;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "Text - Long - Box" type property', function() {
            const property = propertiesInfo.longText;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "Text - Long - HTML Editor" type property', function() {
            const property = propertiesInfo.html;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "List - Single Choice - Radio Button" type property', function() {
            const property = propertiesInfo.list;
            property.listValue = selectors.booleanListValue;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "List - Single Choice - Drop Down" type property', function() {
            const property = propertiesInfo.longList;
            property.listValue = selectors.booleanListValue;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "List - Multiple Choice - Check box" type property', function() {
            const property = propertiesInfo.multiList;
            property.listValue = selectors.booleanListValue;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "List - Multiple Choice - Search Input" type property', function() {
            const property = propertiesInfo.multiSearchList;
            property.listValue = selectors.booleanListValue;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "List - Single Choice - Search Input" type property', function() {
            const property = propertiesInfo.singleSearchList;
            property.listValue = selectors.booleanListValue;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "Calendar" type property', function() {
            const property = propertiesInfo.calendar;

            cy.validateClassProperty(options, property);
        });

        it('Inherits "Password" type property', function() {
            const property = propertiesInfo.password;

            cy.validateClassProperty(options, property);
        });
    });

    describe('Child Item', () => {
        before(() => {
            // Create a new child item
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.addNode(selectors.itemForm, selectors.addItem);
            cy.renameSelectedNode(selectors.itemForm, selectors.editItemUrl, childItemName);

            // Go to edit item
            cy.selectNode(selectors.root, selectors.itemClassForm, className);
            cy.intercept('POST', `**${selectors.editItemUrl}`).as('editItem');
            cy.getSettled(`li [title ="${childItemName}"] a`).last().click();
            cy.wait('@editItem');
        });

        it('child item inherits parent property "List - Multiple Choice - Search Input" and sets value', function() {
            const property = propertiesInfo.multiSearchList;
            const value = ['True', 'False'];

            cy.assignValueToSelect2Property(property, value);
        });

        it('child item inherits parent property "List - Single Choice - Search Input" and sets value', function() {
            const property = propertiesInfo.singleSearchList;
            const value = 'True';

            cy.assignValueToSelect2Property(property, value);
        });

        it('child item inherits parent property "Text - Long - HTML Editor" and sets value', function () {
            const property = propertiesInfo.html;
            const value = `<p>Cypress writing inside ${property.name}</p>`;
            cy.assignValueToCKEditor(property, value);
        });

        it('child item inherits parent property "List - Multiple Choice - Check Box" and sets value', function () {
            const property = propertiesInfo.multiList;
            const value = [selectors.booleanListTrueValue, selectors.booleanListFalseValue];

            cy.assignValueToCheckProperty(property, value);
        });

        it('child item inherits parent property "List - Single Choice - Radio Button" and sets value', function () {
            const property = propertiesInfo.list;
            const value = selectors.booleanListTrueValue;

            cy.assignValueToCheckProperty(property, value);
        });

        it('child item inherits parent property "List - Single Choice - Drop Down" and sets value', function () {
            const property = propertiesInfo.longList;
            const value = selectors.booleanListTrueValue;

            cy.assignValueToSelectProperty(property, value);
        });

        it('child item inherits parent property "Text - Short - Field" and sets value', function () {
            const property = propertiesInfo.text;
            const value = `Cypress writing inside ${property.name}`;

            cy.assignValueToTextProperty(property, value);
        });

        it('child item inherits parent property "Text - Long - Box" and sets value', function () {
            const property = propertiesInfo.longText;
            const value = `Cypress writing inside ${property.name}`;

            cy.assignValueToTextProperty(property, value);
        });

        it('child item inherits parent property "Calendar" and sets value', function () {
            const property = propertiesInfo.calendar;
            const value = `2022-02-02 02:22`;

            cy.assignValueToCalendarProperty(property, value);
        });

        it('child item inherits parent property "Password" and sets value', function () {
            const property = propertiesInfo.password;
            const value = `Cypress writing inside ${property.name}`;

            cy.assignValueToTextProperty(property, value);
            cy.get(`[data-testid="${property.name}"]`).invoke('attr', 'type').should('eq', 'password');
        });

        it('can save update of child item properties', function () {
            cy.intercept('POST', `**${selectors.editItemUrl}`).as('editItem');
            cy.get('.form-toolbar button[data-testid="save"]').scrollIntoView().click();
            cy.wait('@editItem').then(xhr => {
                expect(xhr.response.statusCode).to.eq(200);
            });
        });
    });
});
