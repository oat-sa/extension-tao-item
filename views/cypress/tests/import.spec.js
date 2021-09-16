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
import paths from "../utils/paths";

const className = 'Test E2E class';
const importItemsPath = `${paths.baseItemsPath}/fixtures/packages`;

/**
 * Log in and wait for render
 * After @treeRender click root class
 */
before(() => {
    cy.loginAsAdmin();
    cy.intercept('GET', `**/${selectors.treeRenderUrl}/getOntologyData**`).as('treeRender');
    cy.intercept('POST', `**/${selectors.editClassLabelUrl}`).as('editClassLabel');
    cy.visit(urls.items);
    cy.wait('@treeRender');
    cy.get(`${selectors.root} a`)
        .first()
        .click();
    cy.wait('@editClassLabel');
});

describe('Import', () => {
    const importItemTest = filename => {
        cy.addClassToRoot(
            selectors.root,
            selectors.itemClassForm,
            className,
            selectors.editClassLabelUrl,
            selectors.treeRenderUrl,
            selectors.addSubClassUrl
        );

        cy.selectNode(selectors.root, selectors.itemClassForm, className);

        cy.importToSelectedNode(selectors.importItem, `${importItemsPath}/${filename}`, selectors.importItemUrl, className);

        cy.deleteClassFromRoot(
            selectors.root,
            selectors.itemClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            className,
            selectors.deleteClassUrl,
            true
        );
    };


    it('can import item with shared stimulus', function () {
        importItemTest('test.zip');
    });

    it('can import item', function () {
        importItemTest('e2e_item.zip');
    });

    it('can import item with rich passage', function () {
        importItemTest('e2e_item_rich_passage.zip');
    });

    it('can import item with shared stimulus', function () {
        importItemTest('e2e_item_shared_stimulus.zip');
    });

    it('can import item with shared stimulus', function () {
        importItemTest('test.zip');
    });
});