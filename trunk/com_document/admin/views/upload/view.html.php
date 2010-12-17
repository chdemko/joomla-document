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
 * Upload View of Document component
 */
class DocumentViewUpload extends JView
{
	/**
	 * @since	1.5
	 */
	function __construct($config = null)
	{
		$app = JFactory::getApplication();
		parent::__construct($config);
		$this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
		$this->_addPath('template', JPATH_BASE.'/templates/'.$app->getTemplate().'/html/com_document/default');
	}

	/**
	 * @since	1.5
	 */
	function display($tpl=null)
	{
		// Get data from the model
		$state	= $this->get('State');

		// Are there messages to display ?
		$showMessage	= false;
		if (is_object($state)) {
			$message1		= $state->get('message');
			$message2		= $state->get('extension_message');
			$showMessage	= ($message1 || $message2);
		}

		$this->assign('showMessage',	$showMessage);
		$this->assignRef('state',		$state);

		JHtml::_('behavior.tooltip');
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= documentHelper::getActions();
		JToolBarHelper::title(JText::_('COM_DOCUMENT_UPLOAD_TITLE'), 'install.png');

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_document');
			JToolBarHelper::divider();
		}

		// Document
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_DOCUMENT_UPLOAD_TITLE'));

		//var_dump($document);
		//exit();
	}
}
