<?php


/**
 * @package     Document
 * @subpackage  Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Html Versions class
 *
 * @package     Document
 * @subpackage  Component
 * @since       0.0.1
 */
abstract class DocumentHtmlVersions
{
	/**
	 * Displays the checkbox of a version
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function checkbox($i, $version, $view, $ordering, $direction)
	{
		return JHtml::_('grid.id', $i, $version->id);
	}

	/**
	 * Displays the version number
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function number($i, $version, $view, $ordering, $direction)
	{
		return $version->number;
	}

	/**
	 * Displays the default state of a version
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The document to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function isdefault($i, $version, $view, $ordering, $direction)
	{
		$user = JFactory::getUser();
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $document->checked_out == $user->id || $document->checked_out == 0;
		$canChange = $user->authorise('core.edit.state', 'com_document.document.' . $version->document_id) && $canCheckin;
		return JHtml::_('jgrid.isdefault', $version->isdefault, $i, 'versions.', $canChange && !$version->isdefault);
	}


	/**
	 * Displays the creation date of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function creationDate($i, $version, $view, $ordering, $direction)
	{
		return JHtml::_('date', $version->created, JText::_('DATE_FORMAT_LC4'));
	}

	/**
	 * Displays the creator of a version
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function createdBy($i, $version, $view, $ordering, $direction)
	{
		return $view->escape($version->creator);
	}

	/**
	 * Displays the modification date of a document
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function modificationDate($i, $version, $view, $ordering, $direction)
	{
		return JHtml::_('date', $version->modified, JText::_('DATE_FORMAT_LC4'));
	}

	/**
	 * Displays the modifier of a version
	 *
	 * @param	integer	$i			The row index
	 * @param	object	$version	The version to be displayed.
	 * @param	JView	$view		The view in which the document is displayed.
	 * @param	string	$ordering	The ordering column
	 * @param	string	$direction	The ordering direction
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function modifiedBy($i, $version, $view, $ordering, $direction)
	{
		return $view->escape($version->modifier);
	}
}
