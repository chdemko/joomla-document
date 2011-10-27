<?php

/**
 * @version		$Id: document.php 123 2010-12-17 08:34:28Z raggad $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

/**
 * Document Table class of Document component
 */
class DocumentTableDocument extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent :: __construct('#__document', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	   array		   named array
	 * @return	  null|string	 null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '') {
		if (isset ($array['params']) && is_array($array['params'])) {
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string) $parameter;
		}
		return parent :: bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param	   int $pk primary key
	 * @param	   boolean $reset reset data
	 * @return	  boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true) {
		if (parent :: load($pk, $reset)) {
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadJSON($this->params);
			$this->params = $params;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	  string
	 * @since	   1.6
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_document.message.' . (int) $this-> $k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	  string
	 * @since	   1.6
	 */
	protected function _getAssetTitle() {
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	  int
	 * @since	   1.6
	 */
	protected function _getAssetParentId() {
		$asset = JTable :: getInstance('Asset');
		$asset->loadByName('com_document');
		return $asset->id;
	}

	/**
	 * Method to set the featuring state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pk      An optional array of primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $state   The featuring state. eg. [0 = unfeatured, 1 = featured]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/publish
	 * @since   0.0.1
	 */
	public function feature($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				$this->setError($e);

				return false;
			}
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set('featured = '.(int) $state);

		// Determine if there is checkin support for the table.
		if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
			$query->where('(checked_out = 0 OR checked_out = '.(int) $userId.')');
			$checkin = true;
		}
		else {
			$checkin = false;
		}

		// Build the WHERE clause for the primary keys.
		$query->where($k.' = '.implode(' OR '.$k.' = ', $pks));

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query()) {
			$e = new JException(JText::sprintf('COM_DOCUMENT_DATABASE_ERROR_FEATURE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->featured = $state;
		}

		$this->setError('');
		return true;
	}

	/**
	 * Method to set the featuring state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   integer  $number  The version number
	 * @param   integer  $pk      The primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   0.0.1
	 */
	public function setDefault($number, $pk = null, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		$pk = (int) $pk;
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary key set check to see if the instance key is set.
		if (empty($pk)) {
			if ($this->$k) {
				$pk = $this->$k;
			}
			// Nothing to set version number, return false.
			else {
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				$this->setError($e);

				return false;
			}
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set('version = '.(int) $number);

		// Determine if there is checkin support for the table.
		if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
			$query->where('(checked_out = 0 OR checked_out = '.(int) $userId.')');
			$checkin = true;
		}
		else {
			$checkin = false;
		}

		// Build the WHERE clause for the primary keys.
		$query->where($k.' = '.$pk);

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('COM_DOCUMENT_DATABASE_ERROR_FEATURE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		$this->version = $number;

		$this->setError('');
		return true;
	}
}