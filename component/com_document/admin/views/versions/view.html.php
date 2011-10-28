<?php

/**
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Versions View of Document component
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentViewVersions extends JView
{
	/**
	 * Documents view display method
	 * 
	 * @param string $tpl main layout name
	 */
	public function display($tpl = null)
	{
		// Set the layout
		$this->setLayout('edit');

		// Assign variables
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->item = $this->get('Item');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Set the toolbar
		$this->addToolbar();


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
		// Hide the menu
		JRequest::setVar('hidemainmenu', true);

		$canDo = DocumentHelper::getActions();
		$divider = false;

		JToolBarHelper::title(JText::sprintf('COM_DOCUMENT_TITLE_DOCUMENT', $this->item->id, $this->item->title), 'versions');

		if ($canDo->get('core.create'))
		{
			$divider = true;
			JToolBarHelper::addNew('version.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			$divider = true;
			JToolBarHelper::editList('version.edit');
		}

		if ($divider)
		{		
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::makeDefault('versions.setDefault');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('COM_DOCUMENT_ALERT_VERSIONS_DELETE', 'versions.delete');
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('document.cancel', 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_document');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_DOCUMENT_VERSIONS_MANAGER');
	}
}
