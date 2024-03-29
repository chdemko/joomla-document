<?php
/**
 * @version		$Id: helper.php 172 2010-12-17 14:50:19Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.DS.'components'.DS.'com_document'.DS.'helpers'.DS.'route.php';
jimport('joomla.application.categories');

abstract class modDocumentsCategoriesHelper
{
	public static function getList(&$params)
	{
		$categories = JCategories::getInstance('Document');
		$category = $categories->get($params->get('parent', 'root'));
		$items = $category->getChildren();
		if($params->get('count', 0) > 0 && count($items) > $params->get('count', 0))
		{
			$items = array_slice($items, 0, $params->get('count', 0));
		}
		return $items;
	}

}
