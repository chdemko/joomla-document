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
 * Html Version class
 *
 * @package     Document
 * @subpackage  Component
 * @since       0.0.1
 */
abstract class DocumentHtmlVersion
{
	/**
	 * Displays the the fieldsets of a group
	 *
	 * @param	string	$group		The group name
	 * @param	JView	$view		The view in which the document is displayed.
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function fieldsets($group, $view)
	{
		$html = array();
		foreach ($view->form->getFieldsets($group) as $name => $fieldSet)
		{
			$html[] = '<fieldset class="adminform">';
			$html[] = '<legend>'.JText::_($fieldSet->label).'</legend>';
			$html[] = JHtml::_('DocumentHtml.Version.fields', $name, $fieldSet, $view);
			$html[] = '</fieldset>';
		}
		return implode($html);
	}

	/**
	 * Displays the the sliders of a group
	 *
	 * @param	string	$group		The group name
	 * @param	JView	$view		The view in which the document is displayed.
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function sliders($group, $view)
	{
		$html = array();
		foreach ($view->form->getFieldsets($group) as $name => $fieldSet)
		{
			$html[] = JHtml::_('sliders.panel', JText::_(empty($fieldSet->label) ? ('COM_DOCUMENT_FIELDSET_VERSION_'.$name.'_LABEL') : $fieldSet->label), $name.'-'.$group);
			$html[] = '<fieldset class="panelform">';
			$html[] = JHtml::_('DocumentHtml.Version.fields', $name, $fieldSet, $view);
			$html[] = '</fieldset>';
		}
		return implode($html);
	}

	/**
	 * Displays the the fields of a fieldset
	 *
	 * @param	string	$name		The fieldset name
	 * @param	string	JObject		The fieldset object
	 * @param	JView	$view		The view in which the document is displayed.
	 *
	 * @return	string   The required Html.
	 *
	 * @since	0.0.1
	 */
	public static function fields($name, $fieldSet, $view)
	{
		if (!empty($fieldSet->description))
		{
			$html[] = '<p class="tip">'.$view->escape(JText::_($fieldSet->description)).'</p>'; 
		}
		$html[] = '<ul class="adminformlist">';
		foreach ($view->form->getFieldset($name) as $field)
		{
			$html[] = '<li>'.$field->label.$field->input.'</li>';
		}
		$html[] = '</ul>';
		$html[] = '<div class="clr"></div>';
		return implode($html);
	}
}
