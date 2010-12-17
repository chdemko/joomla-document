<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_document/helpers/route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE.'/components/com_document/models');

abstract class modDocumentsPopularHelper
{
	public static function getList(&$params)
	{
		// Get an instance of the generic documents model
		$model = JModel::getInstance('Documents', 'DocumentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_document')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Filter by language
		$model->setState('filter.language',$app->getLanguageFilter());

		// Ordering
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		$items = $model->getItems();

		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the document
				$item->link = JRoute::_(DocumentHelperRoute::getDocumentRoute($item->slug, $item->catslug));
			}
			else {
				$item->link = JRoute::_('index.php?option=com_user&view=login');
			}
		}

		return $items;
	}
}
