<?php

/**
 * @version		$Id: view.html.php 115 2010-12-17 07:18:21Z tbonnaud $
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
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentViewDocuments extends JView
{
	/**
	 * Documents view display method
	 * 
	 * @param string $tpl main layout name
	 */
	public function display($tpl = null)
	{
		// Assign variables
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Set the toolbar
		$this->addToolbar();

		// Set the submenu
		DocumentHelper::addSubmenu('documents');

		// Prepare the document
		$this->prepareDocument();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Prepare the document
	 */
	protected function prepareDocument()
	{
		JHtml::_('stylesheet','com_document/administrator/documents.css', array(), true);
	}

	/**
	 * Add a toobar
	 */
	protected function addToolbar()
	{
		$canDo = DocumentHelper::getActions();
		$divider = false;

		JToolBarHelper::title(JText::_('COM_DOCUMENT_TITLE_DOCUMENTS'), 'documents');

		JToolBarHelper::custom('documents.download', 'download', 'download', 'COM_DOCUMENT_TASK_DOCUMENTS_DOWNLOAD', true);
		JToolBarHelper::divider();

		if ($canDo->get('core.create'))
		{
			$divider = true;
			JToolBarHelper::addNew('document.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			$divider = true;
			JToolBarHelper::editList('document.edit');
			JToolBarHelper::custom('documents.process', 'refresh', 'refresh', 'COM_DOCUMENT_TASK_DOCUMENTS_PROCESS', true);
		}

		if ($divider)
		{		
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList('documents.publish');
			JToolBarHelper::unpublishList('documents.unpublish');
			JToolBarHelper::archiveList('documents.archive');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::checkin('documents.checkin');
			JToolBarHelper::divider();
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('COM_DOCUMENT_ALERT_DOCUMENTS_DELETE', 'documents.delete');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('documents.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_document');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_DOCUMENT_DOCUMENTS_MANAGER');
	}
}