<?php

/**
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// import the Joomla categories library
jimport('joomla.application.categories');

/**
 * Documents Model of Document component
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentModelDocuments extends JModelList
{
	/**
	 * Valid filter fields or ordering.
	 *
	 * @var    array
	 * @since  0.0.1
	 */
	protected $filter_fields = array (
		'a.title',
		'a.published',
		'a.featured',
		'a.ordering',
		'ag.title',
		'ua.name',
		'a.created',
		'a.hits',
		'l.title',
		'a.id'
	);

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   0.0.1
	 */
	protected function populateState($ordering = 'a.ordering', $direction = 'asc')
	{
		// Initialise variables.

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$authorId = $this->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	0.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.author_id');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery    An SQL query
	 * @since   0.0.1
	 */
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = parent::getListQuery();
		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.id AS id, a.title AS title, a.alias AS alias, a.checked_out AS checked_out, a.checked_out_time AS checked_out_time' .
		', a.published AS published, a.access AS access, a.created AS created, a.created_by AS created_by, a.ordering AS ordering' .
		', a.featured AS featured, a.language AS language, a.hits AS hits, a.version AS version'));
		$query->from('#__document AS a');
		$query->group('a.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the version
		$query->select('MAX(v.number) AS max_version');
		$query->join('LEFT', '`#__document_version` AS v ON a.id = v.document_id');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the users for the creator.
		$query->select('ua.name AS creator');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_by');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Implement View Level Access
		$groups = implode(',', $user->getAuthorisedViewLevels());
		if (!$user->authorise('core.admin'))
		{
			$query->where('a.access IN (' . $groups . ')');
		}

		// Join over the categories.
		$query->join('LEFT', '#__document_category_map AS c2 ON c2.document_id = a.id');
		if (!$user->authorise('core.admin'))
		{
			$query->where('c2.access IN (' . $groups . ')');
		}
		$query->select('GROUP_CONCAT(c2.category_id SEPARATOR ",") AS category_ids');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		else
			if ($published === '')
			{
				$query->where('(a.published = 0 OR a.published = 1)');
			}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (!empty ($categoryId))
		{
			$query->join('LEFT', '#__document_category_map AS c ON c.document_id = a.id');
			if (is_numeric($categoryId))
			{
				$categoryId = array (
					$categoryId
				);
			}
			JArrayHelper::toInteger($categoryId);
			$query->where('c.category_id IN (' . implode(',', $categoryId) . ')');
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId))
		{
			$query->where('a.created_by = ' . (int) $authorId);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty ($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
				if (stripos($search, 'author:') === 0)
				{
					$search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
					$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
				}
				else
				{
					$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
					$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
				}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Gets an array of objects from the results of database query.
	 *
	 * @param   string   $query       The query.
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  array  An array of results.
	 * @since   0.0.1
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$result = parent::_getList($query, $limitstart, $limit);
		foreach ($result as $item)
		{
			$item->categories = array ();
			foreach (explode(',', $item->category_ids) as $catid)
			{
				if ($catid != '')
				{
					$item->categories[] = JCategories::getInstance('Document')->get($catid);
				}
			}
		}
		return $result;
	}
}
