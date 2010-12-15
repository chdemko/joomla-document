<?php

/**
 * @version		$Id$
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die;


/**
 * document component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_document
 * @since		1.6
 */
class documentHelper
{
	public static $extension = 'com_document';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('JGLOBAL_ARTICLES'),
			'index.php?option=com_document&view=documents',
			$vName == 'documents'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_DOCUMENT_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_document',
			$vName == 'categories');
		JSubMenuHelper::addEntry(
			JText::_('COM_DOCUMENT_SUBMENU_FEATURED'),
			'index.php?option=com_document&view=featured',
			$vName == 'featured'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The document ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0, $documentId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($documentId) && empty($categoryId)) {
			$assetName = 'com_document';
		}
		else if (empty($documentId)) {
			$assetName = 'com_document.category.'.(int) $categoryId;
		}
		else {
			$assetName = 'com_document.document.'.(int) $documentId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	* Applies the document tag filters to arbitrary text as per settings for current user group
	* @param text The string to filter
	* @return string The filtered string
	*/
	public static function filterText($text)
	{
		// Filter settings
		jimport('joomla.application.component.helper');
		$config		= JComponentHelper::getParams('com_document');
		$user		= JFactory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $config->get('filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml		= false;
		$whiteList	= false;
		$blackList	= false;
		$unfiltered	= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are include in the Public group as well.
		foreach ($userGroups AS $groupId)
		{
			// May have added a group by not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			else if ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black or white list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array();
				$tempAttributes	= array();

				foreach ($tags AS $tag)
				{
					$tag = trim($tag);

					if ($tag) {
						$tempTags[] = $tag;
					}
				}

				foreach ($attributes AS $attribute)
				{
					$attribute = trim($attribute);

					if ($attribute) {
						$tempAttributes[] = $attribute;
					}
				}

				// Collect the black or white list tags and attributes.
				// Each list is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				else if ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			$filter = JFilterInput::getInstance(array(), array(), 1, 1, 0);
		}
		// Black lists take second precedence.
		else if ($blackList) {
			// Remove the white-listed attributes from the black-list.
			$filter = JFilterInput::getInstance(
				array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
				array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
				1,														// blacklist tags
				1														// blacklist attributes
			);
		}
		// White lists take third precedence.
		else if ($whiteList) {
			$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
		}
		// No HTML takes last place.
		else {
			$filter = JFilterInput::getInstance();
		}

		$text = $filter->clean($text, 'html');

		return $text;
	}
}