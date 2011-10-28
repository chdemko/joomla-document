<?php

/**
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

/**
 * Document Table class of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
 */
class DocumentTableDocument extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__document', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed    $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed    $ignore  An optional array or space separated list of properties
	 *                            to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/bind
	 * @since   0.0.1
	 */
	public function bind($src, $ignore = '')
	{
		if (isset ($src['params']) && is_array($src['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($src['params']);
			$src['params'] = (string) $parameter;
		}
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 * @since	0.0.1
	 */
	protected function _getAssetName()
	{
		return 'com_document.document.' . (int) $this->id;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	0.0.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return  int
	 * @since	0.0.1
	 */
	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance('Asset');
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
		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->id)
			{
				$pks = array($this->id);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				$this->setError($e);
				return false;
			}
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set('featured = '.(int) $state);

		// checkin support for the table.
		$query->where('(checked_out = 0 OR checked_out = '.(int) $userId.')');

		// Build the WHERE clause for the primary keys.
		$query->where('id IN ('.implode(',', $pks).')');

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('COM_DOCUMENT_DATABASE_ERROR_FEATURE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if (count($pks) == $this->_db->getAffectedRows())
		{
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->id, $pks))
		{
			$this->featured = $state;
		}

		return true;
	}

	/**
	 * Method to set the default version for a document
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
		// Sanitize input.
		$pk = (int) $pk;
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary key set check to see if the instance key is set.
		if (empty($pk))
		{
			if ($this->id)
			{
				$pk = $this->id;
			}
			// Nothing to set version number, return false.
			else
			{
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				$this->setError($e);
				return false;
			}
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set('version = CASE WHEN EXISTS (SELECT * FROM #__document_version WHERE document_id = '.(int)$pk.' AND number = '.(int)$number.') THEN '.(int)$number.' ELSE version END');

		// Checkin support for the table.
		$query->where('(checked_out = 0 OR checked_out = '.(int) $userId.')');

		// Build the WHERE clause for the primary keys.
		$query->where('id = '.(int)$pk);

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

		return true;
	}
}
