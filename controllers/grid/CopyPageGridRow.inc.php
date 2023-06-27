<?php

/**
 * @file controllers/grid/CopyPageGridRow.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CopyPageGridRow
 * @ingroup controllers_grid_copyPages
 *
 * @brief Handle custom blocks grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class CopyPageGridRow extends GridRow {

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

		$copyPageId = $this->getId();
		if (!empty($copyPageId)) {
			$router = $request->getRouter();

			// Create the "edit copy page" action
			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editCopyPage',
					new AjaxModal(
						$router->url($request, null, null, 'editCopyPage', null, array('copyPageId' => $copyPageId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);

			// Create the "delete copy page" action
			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'delete', null, array('copyPageId' => $copyPageId)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				)
			);
		}
	}
}

