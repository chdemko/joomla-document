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

abstract class modDocumentsNewsHelper
{
	public static function getList(&$params)
		{
			$app	= JFactory::getApplication();
			$db		= JFactory::getDbo();

			// Get an instance of the generic documents model
			$model = JModel::getInstance('Documents', 'DocumentModel', array('ignore_request' => true));

			// Set application parameters in model
			$appParams = JFactory::getApplication()->getParams();
			$model->setState('params', $appParams);

			// Set the filters based on the module params
			$model->setState('list.start', 0);
			$model->setState('list.limit', (int) $params->get('count', 5));

			$model->setState('filter.published', 1);

			$model->setState('list.select',  'a.id, a.title, a.keywords, a.description, a.author, a.alias, a.filename,  a.catid, a.created, a.created_by, a.created_by_alias,' .
				' a.modified, a.modified_by,a.publish_up, a.publish_down,  a.access,' .
				' a.hits, a.featured,' .
				' LENGTH(a.title) AS readmore');
			// Access filter
			$access = !JComponentHelper::getParams('com_document')->get('show_noauth');
			$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
			$model->setState('filter.access', $access);

			// Category filter
			$model->setState('filter.category_id', $params->get('catid', array()));

			// Filter by language
			$model->setState('filter.language',$app->getLanguageFilter());

			// Set ordering
			$ordering = $params->get('ordering', 'a.publish_up');
			$model->setState('list.ordering', $ordering);
			if (trim($ordering) == 'rand()') {
				$model->setState('list.direction', '');			
			} else {
				$model->setState('list.direction', 'DESC');
			}

			//	Retrieve Content
			$items = $model->getItems();

			foreach ($items as &$item) {
				$item->readmore = (trim($item->fulltext) != '');
				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid.':'.$item->category_alias;

				if ($access || in_array($item->access, $authorised))
				{
					// We know that user has the privilege to view the document
					$item->link = JRoute::_(DocumentHelperRoute::getDocumentRoute($item->slug, $item->catid));
					$item->linkText = JText::_('MOD_DOCUMENTS_NEWS_READMORE');
				}
				else {
					$item->link = JRoute::_('index.php?option=com_user&view=login');
					$item->linkText = JText::_('MOD_DOCUMENTS_NEWS_READMORE_REGISTER');
				}

				//new
				if (!$params->get('image')) {
					$item->introtext = preg_replace('/<img[^>]*>/', '', $item->introtext);
				}

				$results = $app->triggerEvent('onDocumentAfterDisplay', array('com_document.document', &$item, &$params, 1));
				$item->afterDisplayTitle = trim(implode("\n", $results));

				$results = $app->triggerEvent('onDocumentBeforeDisplay', array('com_document.document', &$item, &$params, 1));
				$item->beforeDisplayContent = trim(implode("\n", $results));
			}

			return $items;
		}
}
