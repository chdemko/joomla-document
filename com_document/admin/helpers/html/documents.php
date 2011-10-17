<?php


/**
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Html Documlents class
 *
 * @package     Document
 * @subpackage  Component
 * @since       0.0.1
 */
abstract class DocumentHtmlDocuments
{
	/**
	 * Displays the checkbox of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function checkbox($i, $document, $view, $ordering, $direction)
	{
		return JHtml::_('grid.id', $i, $document->id);
	}

	/**
	 * Displays the title of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function title($i, $document, $view, $ordering, $direction)
	{
		$user = JFactory::getUser();
		$canEdit = $user->authorise('core.edit', 'com_document.document.' . $document->id);
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		$canEditOwn = $user->authorise('core.edit.own', 'com_document.document.' . $document->id) && $document->created_by == $user->id;
		$html[] = '<a href="' . JRoute::_('index.php?option=com_document&task=documents.download&cid[]=' . $document->id) . '">' . JHtml::_('image', 'menu/icon-16-download.png', JText::_('COM_DOCUMENT_ALT_DOWNLOAD'), null, true) . '</a>';
		if ($document->checked_out)
		{
			$html[] = JHtml::_('jgrid.checkedout', $i, $document->editor, $document->checked_out_time, 'documents.', $canCheckin);
		}
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		if ($canEdit || $canEditOwn)
		{
			$html[] = '<a href="' . JRoute::_('index.php?option=com_document&task=document.edit&id=' . $document->id) . '">' . $view->escape($document->title) . '</a>';
		} else
		{
			$html[] = $view->escape($document->title);
		}
		$html[] = '<p class="smallsub">' . JText::sprintf('JGLOBAL_LIST_ALIAS', $view->escape($document->alias)) . '</p>';
		return implode($html);
	}

	/**
	 * Displays the published state of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function published($i, $document, $view, $ordering, $direction)
	{
		$user = JFactory::getUser();
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		$canChange = $user->authorise('core.edit.state', 'com_document.document.' . $document->id) && $canCheckin;
		return JHtml::_('jgrid.published', $document->published, $i, 'documents.', $canChange);
	}

	/**
	 * Displays the featured state of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function featured($i, $document, $view, $ordering, $direction)
	{
		$user = JFactory::getUser();
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		$canChange = $user->authorise('core.edit.state', 'com_document.document.' . $document->id) && $canCheckin;

		$states	= array(
			0 => array('featured', 'COM_DOCUMENT_GRID_UNFEATURED', 'COM_DOCUMENT_TASK_FEATURED', 'COM_DOCUMENT_GRID_UNFEATURED', false, 'unfeatured', 'unfeatured'),
			1 => array('unfeatured', 'COM_DOCUMENT_GRID_FEATURED', 'COM_DOCUMENT_TASK_UNFEATURED', 'COM_DOCUMENT_GRID_FEATURED', false, 'featured', 'featured'),
		);
		return JHtml::_('jgrid.state', $states, (int) $document->featured, $i, 'documents.', $canChange);
	}
	
	/**
	 * Displays the categories of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function categories($i, $document, $view, $ordering, $direction)
	{
		foreach ($document->categories as $cat)
		{
			$tooltip = array ();
			foreach ($cat->getPath() as $i => $parent)
			{
				$tooltip[] = str_repeat('|—', $i) . JCategories::getInstance('Document')->get($parent)->title . '<br />';
			}
			$html[] = JHtml::_('tooltip', implode($tooltip), $cat->title, '', $cat->title);
		}
		return implode('<br />', $html);
	}

	/**
	 * Displays the order of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function order($i, $document, $view, $ordering, $direction)
	{
		$user = JFactory::getUser();
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		$canChange = $user->authorise('core.edit.state', 'com_document.document.' . $document->id) && $canCheckin;
		if ($canChange)
		{
			if ($ordering == 'ordering')
			{
				if ($direction == 'asc')
				{
					$html[] = '<span>' . $view->pagination->orderUpIcon($i, true, 'documents.orderup', 'JLIB_HTML_MOVE_UP', $ordering) . '</span>';
					$html[] = '<span>' . $view->pagination->orderDownIcon($i, $view->pagination->total, true, 'documents.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering) . '</span>';
				}
				elseif ($direction == 'desc')
				{
					$html[] = '<span>' . $view->pagination->orderUpIcon($i, true, 'documents.orderdown', 'JLIB_HTML_MOVE_UP', $ordering) . '</span>';
					$html[] = '<span>' . $view->pagination->orderDownIcon($i, $view->pagination->total, true, 'documents.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering) . '</span>';
				}
				$html[] = '<input type="text" name="order[]" size="5" value="' . $document->ordering . '" disabled="disabled" class="text-area-order" />';
			} else
			{
				$html[] = '<input type="text" name="order[]" size="5" value="' . $document->ordering . '" class="text-area-order" />';
			}
		} else
		{
			$html[] = $document->ordering;
		}
		return implode($html);
	}

	/**
	 * Displays the accees level of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function access($i, $document, $view, $ordering, $direction)
	{
		return $view->escape($document->access_level);
	}

	/**
	 * Displays the author of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function author($i, $document, $view, $ordering, $direction)
	{
		return $view->escape($document->author);
	}

	/**
	 * Displays the creation date of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function date($i, $document, $view, $ordering, $direction)
	{
		return JHtml::_('date', $document->created, JText::_('DATE_FORMAT_LC4'));
	}

	/**
	 * Displays the hits of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function hits($i, $document, $view, $ordering, $direction)
	{
		return (int) $document->hits;
	}

	/**
	 * Displays the language of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function language($i, $document, $view, $ordering, $direction)
	{
		if ($document->language == '*')
		{
			return JText::alt('JALL', 'language');
		} else
		{
			return empty ($document->language_title) ? JText::_('JUNDEFINED') : $view->escape($document->language_title);
		}
	}

	/**
	 * Displays the id of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$document	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function id($i, $document, $view, $ordering, $direction)
	{
		return (int) $document->id;
	}
}