<?php
/**
 * @version		$Id: router.php 170 2010-12-17 14:47:37Z aguibe01 $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_content component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function DocumentBuildRoute(&$query)
{
	$segments	= array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= JComponentHelper::getParams('com_document');
	$advanced	= $params->get('sef_advanced_link', 0);

	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if (isset($query['view'])) {
		$view = $query['view'];
		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		unset($query['view']);
	};

	if (isset($view) && $view == 'document' && isset($query['catid'])) {
		$catid = $query['catid'];
	}
	elseif (isset($view) && $view != 'document' && isset($query['id'])) {
		$catid = $query['id'];
	}
	else {
		$catid = false;
	}

	if (isset($view) and ($view == 'category' or $view == 'document') and $catid) {
		$menuCatid = $mId;
		$categories = JCategories::getInstance('Document');
		$category = $categories->get($catid);

		if ($category) {
			//TODO Throw error that the category either not exists or is unpublished
			$path = array_reverse($category->getPath());

			$array = array();
			foreach($path as $id)
			{
				if ($advanced) {
					list($tmp, $id) = explode(':', $id, 2);
				}
				$array[] = $id;
			}

			$segments = array_merge($segments, array_reverse($array));
		}

		if ($view == 'document') {
			if ($advanced) {
				list($tmp, $id) = explode(':', $query['id'], 2);
			}
			else {
				$id = $query['id'];
			}
			$segments[] = $id;
		}

		unset($query['id']);
		unset($query['catid']);
	}

	if (isset($query['year'])) {
		if (!empty($query['Itemid'])) {
			$segments[] = $query['year'];
			unset($query['year']);
		}
	};

	if (isset($query['month'])) {
		if (!empty($query['Itemid'])) {
			$segments[] = $query['month'];
			unset($query['month']);
		}
	};

	if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		}
		else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	}

	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function DocumentParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_document');
	$advanced = $params->get('sef_advanced_link', 0);

	// Count route segments
	$count = count($segments);

	// Standard routing for articles.
	if (!isset($item)) {
		$vars['view']	= $segments[0];
		$vars['id']		= $segments[$count - 1];

		return $vars;
	}

	// Handle View and Identifier.
	$id = 'root';
	$category = JCategories::getInstance('Document')->get($id);

	if (!$category) {
		die('The category is not published or does not exist');
		//TODO Throw error that the category either not exists or is unpublished
	}

	$categories = $category->getChildren();
	$vars['catid'] = $id;
	$vars['id'] = $id;
	$found = 0;

	foreach($segments as $segment)
	{
		$segment = $advanced ? str_replace(':', '-',$segment) : $segment;
		foreach($categories as $category)
		{
			if ($category->slug == $segment || $category->alias == $segment) {
				$vars['id'] = $category->id;
				$vars['catid'] = $category->id;
				$vars['view'] = 'category';
				$categories = $category->getChildren();
				$found = 1;
				break;
			}
		}

		if ($found == 0) {
			if ($advanced) {
				$db = JFactory::getDBO();
				$query = 'SELECT id FROM #__content WHERE catid = '.$vars['catid'].' AND alias = '.$db->Quote($segment);
				$db->setQuery($query);
				$cid = $db->loadResult();
			}
			else {
				$cid = $segment;
			}

			$vars['id'] = $cid;

			if ($item->query['view'] == 'archive' && $count != 1){
				$vars['year']	= $count >= 2 ? $segments[$count-2] : null;
				$vars['month'] = $segments[$count-1];
				$vars['view']	= 'archive';
			}
			else {
				$vars['view'] = 'document';
			}
		}

		$found = 0;
	}

	return $vars;
}
