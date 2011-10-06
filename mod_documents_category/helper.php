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

jimport('joomla.application.component.model');

$com_path = JPATH_SITE.DS.'components'.DS.'com_document';
require_once $com_path.DS.'router.php';
require_once $com_path.DS.'helpers'.DS.'route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath($com_path.DS.'models');

abstract class modDocumentsCategoryHelper
{
	public static function getList(&$params)
	{
		// Get an instance of the generic documents model
		$documents = JModel::getInstance('Documents', 'DocumentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$documents->setState('params', $appParams);

		// Set the filters based on the module params
		$documents->setState('list.start', 0);
		$documents->setState('list.limit', (int) $params->get('count', 0));
		$documents->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_document')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$documents->setState('filter.access', $access);

		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		switch ($mode)
		{
			case 'dynamic':
				$option = JRequest::getCmd('option');
				$view = JRequest::getCmd('view');
				if ($option === 'com_document') {
					switch($view)
					{
						case 'category':
							$catids = array(JRequest::getInt('id'));
							break;
						case 'categories':
							$catids = array(JRequest::getInt('id'));
							break;
						case 'document':
							if ($params->get('show_on_document_page', 1)) {
								$document_id = JRequest::getInt('id');
								$catid = JRequest::getInt('catid');

								if (!$catid) {
									// Get an instance of the generic document model
									$document = JModel::getInstance('Document', 'DocumentModel', array('ignore_request' => true));

									$document->setState('params', $appParams);
									$document->setState('filter.published', 1);
									$document->setState('document.id', (int) $document_id);
									$item = $document->getItem();

									$catids = array($item->catid);
								}
								else {
									$catids = array($catid);
								}
							}
							else {
								// Return right away if show_on_document_page option is off
								return;
							}
							break;

						case 'featured':
						default:
							// Return right away if not on the category or document views
							return;
					}
				}
				else {
					// Return right away if not on a com_content page
					return;
				}

				break;

			case 'normal':
			default:
				$catids = $params->get('catid');
				$documents->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
				break;
		}

		// Category filter
		if ($catids) {
			if ($params->get('show_child_category_documents', 0) && (int) $params->get('levels', 0) > 0) {
		        // Get an instance of the generic categories model
		        $categories = JModel::getInstance('Categories', 'DocumentModel', array('ignore_request' => true));
		        $categories->setState('params', $appParams);
		        $levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
		        $categories->setState('filter.get_children', $levels);
		        $categories->setState('filter.published', 1);
		        $categories->setState('filter.access', $access);
		        $additional_catids = array();

		        foreach($catids as $catid)
		        {
		            $categories->setState('filter.parentId', $catid);
		            $recursive = true;
		            $items = $categories->getItems($recursive);

		            foreach($items as $category)
		            {
		            	$condition = (($category->level - $categories->getParent()->level) <= $levels);
		            	if ($condition) {
							$additional_catids[] = $category->id;
		            	}

		            }
		        }

		        $catids = array_unique(array_merge($catids, $additional_catids));
		    }

			$documents->setState('filter.category_id', $catids);
		}

		// Ordering
		$documents->setState('list.ordering', $params->get('document_ordering', 'a.ordering'));
		$documents->setState('list.direction', $params->get('document_ordering_direction', 'ASC'));

		// New Parameters
		$documents->setState('filter.featured', $params->get('show_front', 'show'));
		$documents->setState('filter.author_id', $params->get('created_by', ""));
		$documents->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
		$documents->setState('filter.author_alias', $params->get('created_by_alias', ""));
		$documents->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
		$excluded_documents = $params->get('excluded_documents', '');

		if ($excluded_documents) {
			$excluded_documents = explode("\r\n", $excluded_documents);
			$documents->setState('filter.document_id', $excluded_documents);
			$documents->setState('filter.document_id.include', false); // Exclude
		}

		$date_filtering = $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$documents->setState('filter.date_filtering', $date_filtering);
			$documents->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$documents->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$documents->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$documents->setState('filter.relative_date', $params->get('relative_date', 30));
		}

		// Filter by language
		$documents->setState('filter.language',$app->getLanguageFilter());

		$items = $documents->getItems();

		// Display options
		$show_date = $params->get('show_date', 0);
		$show_date_field = $params->get('show_date_field', 'created');
		$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
		$show_category = $params->get('show_category', 0);
		$show_hits = $params->get('show_hits', 0);
		$show_author = $params->get('show_author', 0);
		// Find current Document ID if on an document page
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');

		if ($option === 'com_document' && $view === 'document') {
			$active_document_id = JRequest::getInt('id');
		}
		else {
			$active_document_id = 0;
		}

		// Prepare data for display using display options
		foreach ($items as &$item)
		{
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the document
				$item->link = JRoute::_(DocumentHelperRoute::getDocumentRoute($item->slug, $item->catslug));
			}
			 else {
				// Angie Fixed Routing
				$app	= JFactory::getApplication();
			    $menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
			if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} else if (JRequest::getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
					$Itemid = JRequest::getInt('Itemid');
				}

				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
				}

			// Used for styling the active document
			$item->active = $item->id == $active_document_id ? 'active' : '';

			$item->displayDate = '';
			if ($show_date) {
				$date = new JDate($item->$show_date_field);
				$item->displayDate= $date->format($show_date_format);
			}

			if ($item->catid) {
				$item->displayCategoryLink = JRoute::_(DocumentHelperRoute::getCategoryRoute($item->catid));
				$item->displayCategoryTitle = $show_category ? '<a href="'.$item->displayCategoryLink.'">'.$item->category_title.'</a>' : '';
			}
			else {
				$item->displayCategoryTitle = $show_category ? $item->category_title : '';
			}

			$item->displayHits = $show_hits ? $item->hits : '';
			$item->displayAuthorName = $show_author ? $item->author : '';
			// added Angie show_unauthorizid
			$item->displayReadmore = $item->alternative_readmore;

		}

		return $items;
	}

	/**
	* This is a better truncate implementation than what we
	* currently have available in the library. In particular,
	* on index.php/Banners/Banners/site-map.html JHtml's truncate
	* method would only return "Document...". This implementation
	* was taken directly from the Stack Overflow thread referenced
	* below. It was then modified to return a string rather than
	* print out the output and made to use the relevant JString
	* methods.
	*
	* @link http://stackoverflow.com/questions/1193500/php-truncate-html-ignoring-tags
	* @param mixed $html
	* @param mixed $maxLength
	*/
	public static function truncate($html, $maxLength = 0)
	{
	    $printedLength = 0;
	    $position = 0;
	    $tags = array();

	    $output = '';

	    if (empty($html)) {
			return $output;
	    }

	    while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
	    {
	        list($tag, $tagPosition) = $match[0];

	        // Print text leading up to the tag.
			$str = JString::substr($html, $position, $tagPosition - $position);
	        if ($printedLength + JString::strlen($str) > $maxLength) {
	            $output .= JString::substr($str, 0, $maxLength - $printedLength);
	            $printedLength = $maxLength;
	            break;
	        }

	        $output .= $str;
	        $lastCharacterIsOpenBracket = (JString::substr($output, -1, 1) === '<');

	        if ($lastCharacterIsOpenBracket) {
				$output = JString::substr($output, 0, JString::strlen($output) - 1);
	        }

	        $printedLength += JString::strlen($str);

	        if ($tag[0] == '&') {
	            // Handle the entity.
	            $output .= $tag;
	            $printedLength++;
	        }
	        else {
	            // Handle the tag.
	            $tagName = $match[1][0];

	            if ($tag[1] == '/') {
	                // This is a closing tag.
	                $openingTag = array_pop($tags);

	                $output .= $tag;
	            }
	            else if ($tag[JString::strlen($tag) - 2] == '/') {
	                // Self-closing tag.
	                $output .= $tag;
	            }
	            else {
	                // Opening tag.
	                $output .= $tag;
	                $tags[] = $tagName;
	            }
	        }

	        // Continue after the tag.
	        if ($lastCharacterIsOpenBracket) {
				$position = ($tagPosition - 1) + JString::strlen($tag);
			}
			else {
				$position = $tagPosition + JString::strlen($tag);
			}

	    }

	    // Print any remaining text.
	    if ($printedLength < $maxLength && $position < JString::strlen($html)) {
			$output .= JString::substr($html, $position, $maxLength - $printedLength);
	    }

	    // Close any open tags.
	    while (!empty($tags))
	    {
			$output .= sprintf('</%s>', array_pop($tags));
	    }

	    $length = JString::strlen($output);
	    $lastChar = JString::substr($output, ($length - 1), 1);
	    $characterNumber = ord($lastChar);

	    if ($characterNumber === 194) {
			$output = JString::substr($output, 0, JString::strlen($output) - 1);
	    }

		$output = JString::rtrim($output);

	    return $output.'&hellip;';
	}

	public static function groupBy($list, $fieldName, $document_grouping_direction, $fieldNameToKeep = null)
	{
		$grouped = array();

		if (!is_array($list)) {
			if ($list == '') {
				return $grouped;
			}

			$list = array($list);
		}

		foreach($list as $key => $item)
		{
			if (!isset($grouped[$item->$fieldName])) {
				$grouped[$item->$fieldName] = array();
			}

			if (is_null($fieldNameToKeep)) {
				$grouped[$item->$fieldName][$key] = $item;
			}
			else {
				$grouped[$item->$fieldName][$key] = $item->$fieldNameToKeep;
			}

			unset($list[$key]);
		}

		$document_grouping_direction($grouped);

		return $grouped;
	}

	public static function groupByDate($list, $type = 'year', $document_grouping_direction, $month_year_format = 'F Y')
	{
		$grouped = array();

		if (!is_array($list)) {
			if ($list == '') {
				return $grouped;
			}

			$list = array($list);
		}

		foreach($list as $key => $item)
		{
            switch($type)
            {
				case 'month_year':
					$month_year = JString::substr($item->created, 0, 7);

					if (!isset($grouped[$month_year])) {
						$grouped[$month_year] = array();
					}

					$grouped[$month_year][$key] = $item;
					break;

				case 'year':
				default:
					$year = JString::substr($item->created, 0, 4);

					if (!isset($grouped[$year])) {
						$grouped[$year] = array();
					}

					$grouped[$year][$key] = $item;
					break;
            }

			unset($list[$key]);
		}

		$document_grouping_direction($grouped);

		if ($type === 'month_year') {
			foreach($grouped as $group => $items)
			{
				$date = new JDate($group);
				$formatted_group = $date->format($month_year_format);
				$grouped[$formatted_group] = $items;
				unset($grouped[$group]);
			}
		}

		return $grouped;
	}
}
