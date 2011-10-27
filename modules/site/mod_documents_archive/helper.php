<?php
/**
 * @version		$Id: helper.php 75 2010-12-14 14:32:43Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

abstract class modDocumentsArchiveHelper
{
	function getList(&$params)
	{
		//get database
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
		$query->from('#__document');
		$query->where('published = 2 AND checked_out = 0');
		$query->group('created_year DESC, created_month DESC');

		// Filter by language
		if (JFactory::getApplication()->getLanguageFilter()) {
			$query->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}

		$db->setQuery($query, 0, intval($params->get('count')));
		$rows = $db->loadObjectList();

		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getItems('link', 'index.php?option=com_document&view=archive', true);
		$itemid = isset($item) ? '&Itemid='.$item->id : '';

		$i		= 0;
		$lists	= array();
		foreach ($rows as $row) {
			$date = JFactory::getDate($row->created);

			$created_month	= $date->format("n");
			$month_name		= $date->format("F");
			$created_year	= $date->format("Y");

			$lists[$i]->link	= JRoute::_('index.php?option=com_document&view=archive&year='.$created_year.'&month='.$created_month.$itemid);
			$lists[$i]->text	= JText::sprintf('MOD_DOCUMENTS_ARCHIVE_DATE',$month_name,$created_year);
			$i++;
		}
		return $lists;
	}
}
