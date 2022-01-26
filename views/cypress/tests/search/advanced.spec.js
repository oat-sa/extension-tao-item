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
 import selectorsTAO from '../../../../../tao/views/cypress/utils/selectors';
 import { getRandomNumber } from '../../../../../tao/views/cypress/utils/helpers';

 let isAdvancedSearchEnabled = false;

 describe('Search: Advanced search', () => {
    const randomNumber = getRandomNumber();
    const className = `Test E2E class ${randomNumber}`;
    const options = {
        nodeName: selectors.root,
        className: className,
        nodePropertiesForm: selectors.itemClassForm,
        manageSchemaSelector: selectors.editClass,
        classOptions: selectors.classOptions,
        editUrl: selectors.editClassUrl,
        propertyEditSelector: selectors.propertyEdit,
        propertyListValue: 'Boolean',
        propertyName: `New AdvancedSearch property ${randomNumber}`,
    };

    before(() => {
        cy.intercept('GET', '**/ClientConfig/**').as('getClientConfig');
        cy.setup(
            selectors.treeRenderUrl,
            selectors.editClassLabelUrl,
            urls.items,
            selectors.root
        );
        cy.wait('@getClientConfig').then(function (xhr) {
            // If advanced search extension is installed
            // And disable advanced search feature flag is not set
            if (xhr.response.body.search('taoAdvancedSearch') !== -1
            && xhr.response.body.search('FEATURE_ADVANCED_SEARCH_DISABLED') === -1) {
                isAdvancedSearchEnabled = true;
            }

            if (!isAdvancedSearchEnabled) {
                this.skip();
            }
        });
        cy.addClassToRoot(
            selectors.root,
            selectors.itemClassForm,
            className,
            selectors.editClassLabelUrl,
            selectors.treeRenderUrl,
            selectors.addSubClassUrl
        )
    });

    it('Validate that property is not present in add criteria input', () => {
        cy.searchFor();
        cy.getSettled('.add-criteria-container a:first').click();
        cy.get('#select2-drop-mask');
        cy.contains('.select2-results .select2-result', options.propertyName).should('not.exist');
        // Close select2 dropdown
        cy.get('#select2-drop-mask').click();
        // Close search modal
        cy.getSettled(selectorsTAO.search.modal.closeButton)
        .click();
    });

    it('Validate that property is present in add criteria input', () => {
        // Add property to class
        cy.intercept('POST', `**/${ selectors.editClassUrl }`).as('editClass');
        cy.getSettled(selectors.editClass).click();
        cy.wait('@editClass');
        options.propertyType = propertiesInfo.text.type;
        cy.addPropertyToClass(options);

        // Search for property
        cy.searchFor();
        cy.getSettled('.add-criteria-container a:first').click();
        cy.contains('.select2-results .select2-result', options.propertyName)
            .should('exist')
            .click();
    });

    it('Validate that the property is no longer present after deletion in add criteria input', () => {
        // Close search modal
        cy.getSettled(selectorsTAO.search.modal.closeButton)
        .click();

        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            className,
            selectors.deleteClassUrl,
            true
        );

        cy.searchFor();
        cy.getSettled('.add-criteria-container a:first').click();
        cy.get('#select2-drop-mask');
        cy.contains('.select2-results .select2-result', options.propertyName).should('not.exist');

        cy.get('#select2-drop-mask').click();

        cy.getSettled(selectorsTAO.search.modal.closeButton)
        .click();
    });
});
