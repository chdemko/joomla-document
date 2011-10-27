<?php


/**
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * Versions Model of Document component
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentModelVersions extends JModelList
{
	protected $filter_fields = array('a.number', 'a.created', 'a.modified', 'isdefault', 'ua.name', 'um.name');
		
	protected function populateState($ordering = 'a.created', $direction = 'asc')
	{
		// Initialise variables.

		$id = JRequest::getInt('id');
		$this->setState('document.id', $id);
		
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery    An SQL query
	 */
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = parent::getListQuery();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.document_id, a.number, a.created, a.created_by, a.modified, a.modified_by'
			)
		);
		$query->from('#__document_version AS a');
		$query->where('a.document_id = ' . (int) $this->getState('document.id'));

		// Join over the document table
		$query->leftJoin('#__document as d ON d.id=a.document_id');
		$query->select('d.version=a.number as isdefault');

		// Join over the users for the creator.
		$query->select('ua.name AS creator');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_by');

		// Join over the users for the modifier.
		$query->select('um.name AS modifier');
		$query->join('LEFT', '#__users AS um ON um.id=a.modified_by');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Method to return the current document
	 */
	public function getItem()
	{
		if (!isset($this->item))
		{
			$query = $this->_db->getQuery(true);
			$query->select('*');
			$query->from('#__document');
			$query->where('id = '.(int)$this->getState('document.id'));
			$this->_db->setQuery($query);
			$item = $this->_db->loadObject();

			// Check for a database error.
			if ($this->_db->getErrorNum())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$this->item = $item;		
		}
		return $this->item; 
	}
}