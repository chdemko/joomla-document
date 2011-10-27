<?php

/**
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Document Controller of Document component
 */
class DocumentControllerDocument extends JControllerForm
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $view_item = 'versions';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $view_list = 'documents';


	
	public function upload()
	{
	var_dump(COM_DOCUMENT_BASE);exit();
		//JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user		= JFactory::getUser();

		

		// Get some data from the request
		// FileName, FileSize
		$file		= JRequest::getVar('filedata', '', 'files', 'array');
		// Folder on the server
		$folder		= JRequest::getVar('folder', '', '', 'path');
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Set the redirect
		if ($return) {
			$this->setRedirect(base64_decode($return).'&folder='.$folder);
		}

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				JError::raiseNotice(100, JText::_($err));
				return false;
			}
			
			$filepath = JPath::clean(COM_DOCUMENT_BASE.DS.$folder.DS.strtolower($file['name']));

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('document');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_document.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				JError::raiseWarning(100, JText::plural('COM_DOCUMENT_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			$file = (array) $object_file;

			if (JFile::exists($file['filepath']))
			{
				// File exists
				JError::raiseWarning(100, JText::_('COM_DOCUMENT_ERROR_FILE_EXISTS'));
				return false;
			}


			if (!$user->authorise('core.create', 'com_document'))
			{
				// File does not exist and user is not authorised to create
				JError::raiseWarning(403, JText::_('COM_DOCUMENT_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}
			else
			{
				
			}


			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_DOCUMENT_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_document.file', &$object_file));
				$this->setMessage(JText::sprintf('COM_DOCUMENT_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_DOCUMENT_BASE))));
				return true;
			}
		}

		else
		{
			$this->setRedirect('index.php', JText::_('COM_DOCUMENT_INVALID_REQUEST'), 'error');
			return false;
		}

	}
	
}
