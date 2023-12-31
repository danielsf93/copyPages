<?php

/**
 * @file controllers/grid/CopyPageGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CopyPageGridCellProvider
 * @ingroup controllers_grid_copyPages
 *
 * @brief Class for a cell provider to display information about copy pages
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.linkAction.request.RedirectAction');

class CopyPageGridCellProvider extends GridCellProvider {

	//
	// Template methods from GridCellProvider
	//
	/**
	 * Get cell actions associated with this row/column combination
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @param $position int GRID_ACTION_POSITION_...
	 * @return array an array of LinkAction instances
	 */
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
		$copyPage = $row->getData();

		switch ($column->getId()) {
			case 'path':
				$dispatcher = $request->getDispatcher();
				return array(new LinkAction(
					'details',
					new RedirectAction(
						$dispatcher->url($request, ROUTE_PAGE, null) . '/' . $copyPage->getPath(),
						'copyPage'
					),
					htmlspecialchars($copyPage->getPath())
				));
			default:
				return parent::getCellActions($request, $row, $column, $position);
		}
	}

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$copyPage = $row->getData();

		switch ($column->getId()) {
			case 'path':
				// The action has the label
				return array('label' => '');
			case 'title':
				return array('label' => $copyPage->getLocalizedTitle());
		}
	}
}

