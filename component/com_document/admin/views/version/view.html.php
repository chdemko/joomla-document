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
 * Version View of Document component
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentViewVersion extends JView
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
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

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
		JHtml::_('stylesheet','com_document/administrator/document.css', array(), true);
	}

	/**
	 * Add a toobar
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$checkedOut	= $this->item->checked_out != 0 && $this->item->checked_out != $user->id;
		$canDo		= DocumentHelper::getActions($this->item->id);

		JToolBarHelper::title(JText::sprintf('COM_DOCUMENT_TITLE_VERSION_EDIT', $this->item->document_id, $this->item->number), 'version');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit'))
		{
			JToolBarHelper::apply('version.apply');
			JToolBarHelper::save('version.save');
		}

		JToolBarHelper::cancel('version.cancel', 'JTOOLBAR_CLOSE');

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_DOCUMENT_VERSION_MANAGER');
	}
}
