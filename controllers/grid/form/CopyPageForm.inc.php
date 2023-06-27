<?php

/**
 * @file controllers/grid/form/CopyPageForm.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CopyPageForm
 * @ingroup controllers_grid_copyPages
 *
 * Form for press managers to create and modify sidebar blocks
 *
 */

import('lib.pkp.classes.form.Form');

class CopyPageForm extends Form {
	/** @var int Context (press / journal) ID */
	var $contextId;

	/** @var string Copy page name */
	var $copyPageId;

	/** @var CopyPagesPlugin Copy pages plugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $copyPagesPlugin CopyPagesPlugin The copy page plugin
	 * @param $contextId int Context ID
	 * @param $copyPageId int Copy page ID (if any)
	 */
	function __construct($copyPagesPlugin, $contextId, $copyPageId = null) {
		parent::__construct($copyPagesPlugin->getTemplateResource('editCopyPageForm.tpl'));

		$this->contextId = $contextId;
		$this->copyPageId = $copyPageId;
		$this->plugin = $copyPagesPlugin;

		// Add form checks
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
		$this->addCheck(new FormValidator($this, 'title', 'required', 'plugins.generic.copyPages.nameRequired'));
		$this->addCheck(new FormValidatorRegExp($this, 'path', 'required', 'plugins.generic.copyPages.pathRegEx', '/^[a-zA-Z0-9\/._-]+$/'));
		$form = $this;
		$this->addCheck(new FormValidatorCustom($this, 'path', 'required', 'plugins.generic.copyPages.duplicatePath', function($path) use ($form) {
			$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
			$page = $copyPagesDao->getByPath($form->contextId, $path);
			return !$page || $page->getId()==$form->copyPageId;
		}));
	}

	/**
	 * Initialize form data from current group group.
	 */
	function initData() {
		$templateMgr = TemplateManager::getManager();
		if ($this->copyPageId) {
			$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
			$copyPage = $copyPagesDao->getById($this->copyPageId, $this->contextId);
			$this->setData('path', $copyPage->getPath());
			$this->setData('title', $copyPage->getTitle(null)); // Localized
			$this->setData('content', $copyPage->getContent(null)); // Localized
		}

	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('path', 'title', 'content'));
	}

	/**
	 * @copydoc Form::fetch
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign(array(
			'copyPageId' => $this->copyPageId,
			'pluginJavaScriptURL' => $this->plugin->getJavaScriptURL($request),
		));

		if ($context = $request->getContext()) $templateMgr->assign('allowedVariables', array(
			'contactName' => __('plugins.generic.tinymce.variables.principalContactName', array('value' => $context->getData('contactName'))),
			'contactEmail' => __('plugins.generic.tinymce.variables.principalContactEmail', array('value' => $context->getData('contactEmail'))),
			'supportName' => __('plugins.generic.tinymce.variables.supportContactName', array('value' => $context->getData('supportName'))),
			'supportPhone' => __('plugins.generic.tinymce.variables.supportContactPhone', array('value' => $context->getData('supportPhone'))),
			'supportEmail' => __('plugins.generic.tinymce.variables.supportContactEmail', array('value' => $context->getData('supportEmail'))),
		));

		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save form values into the database
	 */
	function execute(...$functionParams) {
		parent::execute(...$functionParams);

		$copyPagesDao = DAORegistry::getDAO('CopyPagesDAO');
		if ($this->copyPageId) {
			// Load and update an existing page
			$copyPage = $copyPagesDao->getById($this->copyPageId, $this->contextId);
		} else {
			// Create a new copy page
			$copyPage = $copyPagesDao->newDataObject();
			$copyPage->setContextId($this->contextId);
		}

		$copyPage->setPath($this->getData('path'));
		$copyPage->setTitle($this->getData('title'), null); // Localized
		$copyPage->setContent($this->getData('content'), null); // Localized

		if ($this->copyPageId) {
			$copyPagesDao->updateObject($copyPage);
		} else {
			$copyPagesDao->insertObject($copyPage);
		}
	}
}

