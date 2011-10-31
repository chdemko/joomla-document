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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
 */
class DocumentController extends JController
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $default_view = 'documents';

	/**
	 * Display method
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining.
	 * @since   0.0.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = JRequest::getCmd('view');
		$error = false;
		switch ($view)
		{
			case 'version':
				$id = JRequest::getInt('id');
				$error = !$this->checkEditId('com_document.edit.version', $id);
			break;
			case 'versions':
				$id = JRequest::getInt('id', JFactory::getApplication()->getUserState('com_document.versions.document.id'));
				$error = !$this->checkEditId('com_document.edit.document', $id);
			break;
		}
		
		if ($error)
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_document', false));

			return $this;
		}
		else
		{
			return parent::display($cachable, $urlparams);
		}
	}
}
