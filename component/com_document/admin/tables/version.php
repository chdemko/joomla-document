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
 * Version Table class of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
 */
class DocumentTableVersion extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object JDatabase connector object.
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__document_version', 'id', $db);
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
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link	http://docs.joomla.org/JTable/store
	 * @since   0.0.1
	 */
	public function store($updateNulls = false)
	{
		// Get the date, the user and the db
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Set the 'modified' and the 'modified by' fields
		$this->modified	 = $date->format($db->getDateFormat());
		$this->modified_by = $user->id;

		if (empty($this->id))
		{
			// Set the 'created' and the 'created by' fields
			$this->created	 = $date->format($db->getDateFormat());
			$this->created_by = $user->id;	
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}
}
