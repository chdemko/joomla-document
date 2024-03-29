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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Versions Controller of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
 */
class DocumentControllerVersions extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $text_prefix = 'COM_DOCUMENT_VERSIONS';

	/**
	 * Proxy for getModel.
	 *
	 * @since	0.0.1
	 */
	public function getModel($name = 'Version', $prefix = 'DocumentModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/**
	 * Method to change the default version
	 *
	 * @return  void
	 *
	 * @since   0.0.1
	 */
	public function setDefault()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		// Get items to set default from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$id	= JRequest::getInt('id', 0);

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel('Document');

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Feature the items.
			if (!$model->setDefault($id, $cid[0]))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(JText::_('COM_DOCUMENT_VERSIONS_MESSAGE_SET_DEFAULT'));
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=com_document&view=versions&layout=edit&id='.(int)$id, false));
	}
}
