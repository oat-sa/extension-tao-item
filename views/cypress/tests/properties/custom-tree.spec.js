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
import { getRandomNumber } from '../../../../../tao/views/cypress/utils/helpers';

describe('Resource properties - Cycle through simple types', () => {
    const className = `Test E2E class ${getRandomNumber()}`;
    const childItemName = 'Test E2E child item';
    const childClassName = 'Test E2E child class';
    const treePath = '../../tao/views/cypress/fixtures/math_grade_1_1642670091.rdf';
    const treeName = 'MATH Grade 1'; // Defined in the tree file
    const treeLength = 40; // Defined in the tree file
    const options = {
        nodeName: selectors.root,
        className: className,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit,
        propertyListValue: treeName,
    };

    /**
     * Log in and wait for render, create tree (if not exists)
     * After @treeRender click root class
     */
    before(() => {
        cy.loginAsAdmin();

        // Create a tree
        cy.intercept('GET', '**/Trees/getTreeData?*').as('getTrees')
        cy.visit(urlsTAO.settings.tree);
        cy.wait('@getTrees');
        cy.getSettled('#tree-taoBo_tree')
            .then(($treesResources) => {
                // Avoid double importing of the same file
                if($treesResources.find(`li[data-uri]:contains("${treeName}")`).length === 0) {
                    cy.importToRootTree(treePath);
                } else {
                    cy.log(`Tree file ${treeName} already exists`);
                }
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
        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            className,
            selectors.deleteClassUrl,
            true
        );
        // TODO: Delete created tree (when feature will be available)
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

        it('can edit and add new "Tree - Multiple node choice" type property to the item class', function () {
            options.propertyName = propertiesInfo.multipleNodeTree.name;
            options.propertyType = propertiesInfo.multipleNodeTree.type;

            cy.addPropertyToClass(options).its('response.statusCode');
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

            it('Inherits "Tree - Multiple node choice" type property', function() {
                const property = propertiesInfo.multipleNodeTree;
                property.label = treeName; // Validate by name

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

            it('"Tree - Multiple node choice" shown as a list', function () {
                const property = propertiesInfo.multipleNodeTree;
                cy.getSettled('label').contains(property.name).parent().find('li a').should('have.length', treeLength);
            });

            it('"Tree - Multiple node choice" property selected', function () {
                const property = propertiesInfo.multipleNodeTree;
                cy.getSettled('label').contains(property.name).parent().find('li:first-child a').click();
                cy.getSettled('label').contains(property.name).parent().find('li a.clicked.checked').should('have.length', 1);
            });

            it('can save update of child item properties', function () {
                cy.intercept('POST', `**${selectors.editItemUrl}`).as('editItem');
                cy.get('.form-toolbar button[data-testid="save"]').click();
                cy.wait('@editItem').then(xhr => {
                    expect(xhr.response.statusCode).to.eq(200);
                });
            });
        });
    });
});
