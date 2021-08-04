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


describe('Tests', () => {
    const className = 'Test E2E class';
    const classMovedName = 'Test E2E class Moved';

    /**
     * Visit the page
     */
    beforeEach(() => {
        cy.visit(urls.tests);
    });

    /**
     * Log in
     */
    before(() => {
        cy.loginAsAdmin();
    });

    /**
     * Tests
     */
    describe('Test creation, editing and deletion', () => {
        it('can create a new test class', function () {
            cy.addClassToRoot(selectors.root, selectors.testClassForm, className);
        });

        it('can create and rename a new test', function () {
            cy.selectNode(selectors.root, selectors.testClassForm, className);
            cy.addNode(selectors.testForm, selectors.addTest);
            cy.renameSelected(selectors.testForm, 'Test E2E test 1');
        });

        it('can delete test', function () {
            cy.selectNode(selectors.root, selectors.testClassForm, className);
            cy.addNode(selectors.testForm, selectors.addTest);
            cy.renameSelected(selectors.testForm, 'Test E2E test 2');
            cy.deleteNode(selectors.deleteTest, 'Test E2E test 2');
        });

        it('can delete test class', function () {
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.testClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className
            );
        });

        it('can delete empty test class', function () {
            cy.addClassToRoot(selectors.root, selectors.testClassForm, className);
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.testClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className
            );
        });

        it('can move test class', function () {
            cy.moveClassFromRoot(
                selectors.root,
                selectors.testClassForm,
                selectors.moveClass,
                selectors.moveConfirmSelector,
                className,
                classMovedName
            );
        });
    });

    after(() => {
        cy.deleteClassFromRoot(
            selectors.root,
            selectors.testClassForm,
            selectors.deleteClass,
            selectors.deleteConfirm,
            classMovedName
        );
    });
});
