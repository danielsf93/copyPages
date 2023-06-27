/**
 * @file cypress/tests/functional/CopyPages.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 */

describe('Copy Pages plugin tests', function() {
	it('Creates and exercises a copy page', function() {
		cy.login('admin', 'admin', 'publicknowledge');

		cy.get('.app__nav a').contains('Website').click();
		cy.get('button[id="plugins-button"]').click();

		// Find and enable the plugin
		cy.get('input[id^="select-cell-copypagesplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Copy Pages Plugin" has been enabled.\')');

		// Check for a 404 on the page we are about to create
		cy.visit('/index.php/publicknowledge/flarm', {failOnStatusCode: false});
		cy.get('h1:contains("404 Not Found")');

		// Find the plugin's tab
		cy.visit('');
		cy.get('a:contains("admin")').click();
		cy.get('ul[id="navigationUser"] a:contains("Dashboard")').click();
		cy.get('.app__nav a').contains('Website').click();
		cy.get('button[id="copyPages-button"]').click();

		// Create a copy page
		cy.get('a[id^="component-plugins-generic-copypages-controllers-grid-copypagegrid-addCopyPage-button-"]').click();
		cy.get('form[id="copyPageForm"] input[id^="path-"]').type('flarm');
		cy.get('form[id^="copyPageForm"] input[id^="title-en_US-"]').type('Test Copy Page');
		cy.get('textarea[id^="content-en_US-"]').then(node => {
			cy.setTinyMceContent(node.attr('id'), 'Here is my new copy page.');
		});
		cy.get('form[id="copyPageForm"] button[id^="submitFormButton-"]').click();
		cy.waitJQuery();

		// View the copy page
		cy.visit('/index.php/publicknowledge/flarm');
		cy.get('h2:contains("Test Copy Page")');
		cy.get('p:contains("Here is my new copy page.")');
	});
})
