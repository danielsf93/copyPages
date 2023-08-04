<?php

/**
 * @file classes/CopyPagesDAO.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.copyPages
 * @class CopyPagesDAO
 * Operations for retrieving and modifying CopyPages objects.
 */

import('lib.pkp.classes.db.DAO');
import('plugins.generic.copyPages.classes.CopyPage');

class CopyPagesDAO extends DAO {

	/**
	 * Get a copy page by ID
	 * @param $copyPageId int Copy page ID
	 * @param $contextId int Optional context ID
	 */
	function getById($copyPageId, $contextId = null) {
		$params = [(int) $copyPageId];
		if ($contextId) $params[] = (int) $contextId;

		$result = $this->retrieve(
			'SELECT * FROM static_pages WHERE static_page_id = ?'
			. ($contextId?' AND context_id = ?':''),
			$params
		);
		$row = $result->current();
		return $row ? $this->_fromRow((array) $row) : null;
	}

	/**
	 * Get a set of copy pages by context ID
	 * @param $contextId int
	 * @param $rangeInfo Object optional
	 * @return DAOResultFactory
	 */
	function getByContextId($contextId, $rangeInfo = null) {
		$result = $this->retrieveRange(
			'SELECT * FROM static_pages WHERE context_id = ?',
			[(int) $contextId],
			$rangeInfo
		);
		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Get a copy page by path.
	 * @param $contextId int Context ID
	 * @param $static_page_id string Path
	 * @return CopyPage
	 */
	function getByPath($contextId, $static_page_id) {
		$result = $this->retrieve(
			'SELECT * FROM static_pages WHERE context_id = ? AND path = ?',
			[(int) $contextId, $path]
		);
		$row = $result->current();
		return $row ? $this->_fromRow((array) $row) : null;
	}

	/**
	 * Insert a copy page.
	 * @param $copyPage CopyPage
	 * @return int Inserted copy page ID
	 */
	function insertObject($copyPage) {
		$this->update(
			'INSERT INTO static_pages (context_id, path) VALUES (?, ?)',
			[(int) $copyPage->getContextId(), $copyPage->getPath()]
		);

		$copyPage->setId($this->getInsertId());
		$this->updateLocaleFields($copyPage);

		return $copyPage->getId();
	}

	/**
	 * Update the database with a copy page object
	 * @param $copyPage CopyPage
	 */
	function updateObject($copyPage) {
		$this->update(
			'UPDATE	static_pages
			SET	context_id = ?,
				path = ?
			WHERE	static_page_id = ?',
			[
				(int) $copyPage->getContextId(),
				$copyPage->getPath(),
				(int) $copyPage->getId()
			]
		);
		$this->updateLocaleFields($copyPage);
	}

	/**
	 * Delete a copy page by ID.
	 * @param $copyPageId int
	 */
	function deleteById($copyPageId) {
		$this->update(
			'DELETE FROM static_pages WHERE static_page_id = ?',
			[(int) $copyPageId]
		);
		$this->update(
			'DELETE FROM static_page_settings WHERE static_page_id = ?',
			[(int) $copyPageId]
		);
	}

	/**
	 * Delete a copy page object.
	 * @param $copyPage CopyPage
	 */
	function deleteObject($copyPage) {
		$this->deleteById($copyPage->getId());
	}

	/**
	 * Generate a new copy page object.
	 * @return CopyPage
	 */
	function newDataObject() {
		return new CopyPage();
	}

	/**
	 * Return a new copy pages object from a given row.
	 * @return CopyPage
	 */
	function _fromRow($row) {
		$copyPage = $this->newDataObject();
		$copyPage->setId($row['static_page_id']);
		$copyPage->setPath($row['path']);
		$copyPage->setContextId($row['context_id']);

		$this->getDataObjectSettings('static_page_settings', 'static_page_id', $row['static_page_id'], $copyPage);
		return $copyPage;
	}

	/**
	 * Get the insert ID for the last inserted copy page.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('static_pages', 'static_page_id');
	}

	/**
	 * Get field names for which data is localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return ['title', 'content'];
	}

	/**
	 * Update the localized data for this object
	 * @param $author object
	 */
	function updateLocaleFields(&$copyPage) {
		$this->updateDataObjectSettings('static_page_settings', $copyPage,
			['static_page_id' => $copyPage->getId()]
		);
	}
}

