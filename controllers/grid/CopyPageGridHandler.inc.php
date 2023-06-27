<?php

/**
 * @file controllers/grid/CopyPageGridHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CopyPageGridHandler
 * @ingroup controllers_grid_copyPages
 *
 * @brief Handle copy pages grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.copyPages.controllers.grid.CopyPageGridRow');
import('plugins.generic.copyPages.controllers.grid.CopyPageGridCellProvider');

class CopyPageGridHandler extends GridHandler {
	/** @var CopyPagesPlugin The copy pages plugin */
	static $plugin;

	/**
	 * Set the copy pages plugin.
	 * @param $plugin CopyPagesPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('index', 'fetchGrid', 'fetchRow', 'addCopyPage', 'editCopyPage', 'updateCopyPage', 'delete')
		);
	}


	//
	// Overridden template methods
	//
	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.ContextAccessPolicy');
		$this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @copydoc GridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);
		$context = $request->getContext();

		// Set the grid details.
		$this->setTitle('plugins.generic.copyPages.copyPages');
		$this->setEmptyRowText('plugins.generic.copyPages.noneCreated');

		// Get the pages and add the data to the grid
		$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
		$this->setGridDataElements($copyPagesDao->getByContextId($context->getId()));

		// Add grid-level actions
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addCopyPage',
				new AjaxModal(
					$router->url($request, null, null, 'addCopyPage'),
					__('plugins.generic.copyPages.addCopyPage'),
					'modal_add_item'
				),
				__('plugins.generic.copyPages.addCopyPage'),
				'add_item'
			)
		);

		// Columns
		$cellProvider = new CopyPageGridCellProvider();
		$this->addColumn(new GridColumn(
			'title',
			'plugins.generic.copyPages.pageTitle',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'path',
			'plugins.generic.copyPages.path',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	function getRowInstance() {
		return new CopyPageGridRow();
	}

	//
	// Public Grid Actions
	//
	/**
	 * Display the grid's containing page.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function index($args, $request) {
		$context = $request->getContext();
		import('lib.pkp.classes.form.Form');
		$form = new Form(self::$plugin->getTemplateResource('copyPages.tpl'));
		return new JSONMessage(true, $form->fetch($request));
	}

	/**
	 * An action to add a new custom copy page
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 */
	function addCopyPage($args, $request) {
		// Calling editCopyPage with an empty ID will add
		// a new copy page.
		return $this->editCopyPage($args, $request);
	}

	/**
	 * An action to edit a copy page
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 * @return string Serialized JSON object
	 */
	function editCopyPage($args, $request) {
		$copyPageId = $request->getUserVar('copyPageId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.copyPages.controllers.grid.form.CopyPageForm');
		$copyPagesPlugin = self::$plugin;
		$copyPageForm = new CopyPageForm(self::$plugin, $context->getId(), $copyPageId);
		$copyPageForm->initData();
		return new JSONMessage(true, $copyPageForm->fetch($request));
	}

	/**
	 * Update a custom block
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateCopyPage($args, $request) {
		$copyPageId = $request->getUserVar('copyPageId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and populate the form
		import('plugins.generic.copyPages.controllers.grid.form.CopyPageForm');
		$copyPagesPlugin = self::$plugin;
		$copyPageForm = new CopyPageForm(self::$plugin, $context->getId(), $copyPageId);
		$copyPageForm->readInputData();

		// Check the results
		if ($copyPageForm->validate()) {
			// Save the results
			$copyPageForm->execute();
 			return DAO::getDataChangedEvent();
		} else {
			// Present any errors
			return new JSONMessage(true, $copyPageForm->fetch($request));
		}
	}

	/**
	 * Delete a copy page
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function delete($args, $request) {
		$copyPageId = $request->getUserVar('copyPageId');
		$context = $request->getContext();

		// Delete the copy page
		$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
		$copyPage = $copyPagesDao->getById($copyPageId, $context->getId());
		$copyPagesDao->deleteObject($copyPage);

		return DAO::getDataChangedEvent();
	}
}

