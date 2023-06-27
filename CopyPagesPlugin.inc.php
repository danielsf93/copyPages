<?php

/**
 * @file CopyPagesPlugin.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.copyPages
 * @class CopyPagesPlugin
 * Copy pages plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class CopyPagesPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('copypages');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		$description = __('copypages1');
		if (!$this->isTinyMCEInstalled())
			$description .= __('copypages2');
		return $description;
	}

	/**
	 * Check whether or not the TinyMCE plugin is installed.
	 * @return boolean True iff TinyMCE is installed.
	 */
	function isTinyMCEInstalled() {
		$application = Application::get();
		$products = $application->getEnabledProducts('plugins.generic');
		return (isset($products['tinymce']));
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled($mainContextId)) {
				// Register the copy pages DAO.
				import('plugins.generic.copyPages.classes.CopyPagesDAO');
				$copyPagesDao = new CopyPagesDAO();
				DAORegistry::registerDAO('CopyPagesDAO', $copyPagesDao);

				HookRegistry::register('Template::Settings::website', array($this, 'callbackShowWebsiteSettingsTabs'));

				// Intercept the LoadHandler hook to present
				// copy pages when requested.
				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));

				// Register the components this plugin implements to
				// permit administration of copy pages.
				HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Extend the website settings tabs to include copy pages
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackShowWebsiteSettingsTabs($hookName, $args) {
		$templateMgr = $args[1];
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();

		$output .= $templateMgr->fetch($this->getTemplateResource('copyPagesTab.tpl'));

		// Permit other plugins to continue interacting with this hook
		return false;
	}

	/**
	 * Declare the handler function to process the actual page PATH
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackHandleContent($hookName, $args) {
		$request = Application::get()->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page =& $args[0];
		$op =& $args[1];

		$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
		if ($page == 'pages' && $op == 'preview') {
			// This is a preview request; mock up a copy page to display.
			// The handler class ensures that only managers and administrators
			// can do this.
			$copyPage = $copyPagesDao->newDataObject();
			$copyPage->setContent((array) $request->getUserVar('content'), null);
			$copyPage->setTitle((array) $request->getUserVar('title'), null);
		} else {
			// Construct a path to look for
			$path = $page;
			if ($op !== 'index') $path .= "/$op";
			if ($ops = $request->getRequestedArgs()) $path .= '/' . implode('/', $ops);

			// Look for a copy page with the given path
			$context = $request->getContext();
			$copyPage = $copyPagesDao->getByPath(
				$context?$context->getId():CONTEXT_ID_NONE,
				$path
			);
		}

		// Check if this is a request for a copy page or preview.
		if ($copyPage) {
			// Trick the handler into dealing with it normally
			$page = 'pages';
			$op = 'view';

			// It is -- attach the copy pages handler.
			define('HANDLER_CLASS', 'CopyPagesHandler');
			$this->import('CopyPagesHandler');

			// Allow the copy pages page handler to get the plugin object
			CopyPagesHandler::setPlugin($this);
			CopyPagesHandler::setPage($copyPage);
			return true;
		}
		return false;
	}

	/**
	 * Permit requests to the copy pages grid handler
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.copyPages.controllers.grid.CopyPageGridHandler') {
			// Allow the copy page grid handler to get the plugin object
			import($component);
			CopyPageGridHandler::setPlugin($this);
			return true;
		}
		return false;
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $actionArgs) {
		$dispatcher = $request->getDispatcher();
		import('lib.pkp.classes.linkAction.request.RedirectAction');
		return array_merge(
			$this->getEnabled()?[
				new LinkAction(
					'settings',
					new RedirectAction($dispatcher->url(
						$request, ROUTE_PAGE,
						null, 'management', 'settings', 'website',
						array('uid' => uniqid()), // Force reload
						'copyPages' // Anchor for tab
					)),
					__('plugins.generic.copyPages.editAddContent'),
					null
				),
			]:[],
			parent::getActions($request, $actionArgs)
		);
	}

	/**
	 * @copydoc Plugin::getInstallMigration()
	 */
	function getInstallMigration() {
		$this->import('CopyPagesSchemaMigration');
		return new CopyPagesSchemaMigration();
	}

	/**
	 * Get the JavaScript URL for this plugin.
	 */
	function getJavaScriptURL($request) {
		return $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js';
	}
}
