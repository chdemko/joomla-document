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
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Documents View of Document component
 */
class DocumentViewDocuments extends JView
{
	protected $state;
	protected $items;
	protected $pagination;
	
	/**
	 * Documents view display method
	 */
	function display($tpl = null) 
	{
		$this->state= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		// Set the toolbar
		$this->addToolbar();
		// Display the template
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		$canDo	= documentHelper::getActions($this->state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_DOCUMENT_DOCUMENTS_TITLE'), 'document.png');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('document.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('document.edit','JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('documents.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('documents.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('documents.archive','JTOOLBAR_ARCHIVE');
			JToolBarHelper::custom('documents.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'documents.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('documents.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_document', 550, 800);
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_DOCUMENT_DOCUMENTS_MANAGER');
	}
}
