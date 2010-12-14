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
 * Document component helper.
 */
abstract class DocumentHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
JSubMenuHelper::addEntry(JText::_('COM_DOCUMENT_SUBMENU_MESSAGES'), 'index.php?option=com_DOCUMENT', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_DOCUMENT_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_DOCUMENT', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-DOCUMENT {background-image: url(../media/com_DOCUMENT/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_DOCUMENT_ADMINISTRATION_CATEGORIES'));
	}
}
