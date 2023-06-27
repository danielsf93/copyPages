<?php

/**
 * @file CopyPagesHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.copyPages
 * @class CopyPagesHandler
 * Find copy page content and display it when requested.
 */

import('classes.handler.Handler');

class CopyPagesHandler extends Handler {
	/** @var CopyPagesPlugin The copy pages plugin */
	static $plugin;

	/** @var CopyPage The copy page to view */
	static $copyPage;


	/**
	 * Provide the copy pages plugin to the handler.
	 * @param $plugin CopyPagesPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Set a copy page to view.
	 * @param $copyPage CopyPage
	 */
	static function setPage($copyPage) {
		self::$copyPage = $copyPage;
	}

	/**
	 * Handle index request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function index($args, $request) {
		$request->redirect(null, null, 'view', $request->getRequestedOp());
	}

	/**
	 * Handle view page request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function view($args, $request) {
		$path = array_shift($args);

		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
		$context = $request->getContext();
		$contextId = $context?$context->getId():CONTEXT_ID_NONE;

		// Ensure that if we're previewing, the current user is a manager or admin.
		$roles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
		if (!self::$copyPage->getId() && count(array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SITE_ADMIN), $roles))==0) {
			fatalError('The current user is not permitted to preview.');
		}

		// Assign the template vars needed and display
		$templateMgr = TemplateManager::getManager($request);
		$this->setupTemplate($request);
		$templateMgr->assign('title', self::$copyPage->getLocalizedTitle());

		$vars = array();
		if ($context) $vars = array(
			'{$contactName}' => $context->getData('contactName'),
			'{$contactEmail}' => $context->getData('contactEmail'),
			'{$supportName}' => $context->getData('supportName'),
			'{$supportPhone}' => $context->getData('supportPhone'),
			'{$supportEmail}' => $context->getData('supportEmail'),
		);
		$templateMgr->assign('content', strtr(self::$copyPage->getLocalizedContent(), $vars));

		$templateMgr->display(self::$plugin->getTemplateResource('content.tpl'));
	}
}

