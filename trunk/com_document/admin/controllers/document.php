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

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Document Controller of Document component
 */
class DocumentControllerDocument extends JControllerForm
{
	 /**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function edit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_document.document.'.$recordId)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_document.document.'.$recordId)) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	
	
	
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
	
	
	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 * @since 1.5
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();

		// Get some data from the request
		$tmpl	= JRequest::getCmd('tmpl');
		$paths	= JRequest::getVar('rm', array(), '', 'array');
		$folder = JRequest::getVar('folder', '', '', 'path');

		if ($tmpl == 'component') {
			// We are inside the iframe
			$this->setRedirect('index.php?option=com_document&view=mediaList&folder='.$folder.'&tmpl=component');
		} else {
			$this->setRedirect('index.php?option=com_document&folder='.$folder);
		}

		if (!$user->authorise('core.delete','com_document'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}
		else
		{
			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			// Initialise variables.
			$ret = true;

			if (count($paths))
			{
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				foreach ($paths as $path)
				{
					if ($path !== JFile::makeSafe($path))
					{
						// filename is not safe
						$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
						JError::raiseWarning(100, JText::sprintf('COM_DOCUMENT_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
						continue;
					}

					$fullPath = JPath::clean(COM_MEDIA_BASE.DS.$folder.DS.$path);
					$object_file = new JObject(array('filepath' => $fullPath));
					if (is_file($fullPath))
					{
						// Trigger the onContentBeforeDelete event.
						$result = $dispatcher->trigger('onContentBeforeDelete', array('com_document.file', &$object_file));
						if (in_array(false, $result, true)) {
							// There are some errors in the plugins
							JError::raiseWarning(100, JText::plural('COM_DOCUMENT_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
							continue;
						}

						$ret &= JFile::delete($fullPath);

						// Trigger the onContentAfterDelete event.
						$dispatcher->trigger('onContentAfterDelete', array('com_document.file', &$object_file));
						$this->setMessage(JText::sprintf('COM_DOCUMENT_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
					}
					else if (is_dir($fullPath))
					{
						if (count(JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS','.DS_Store','__MACOSX'), array('index.html', '^\..*','.*~'))) == 0)
						{
							// Trigger the onContentBeforeDelete event.
							$result = $dispatcher->trigger('onContentBeforeDelete', array('com_document.folder', &$object_file));
							if (in_array(false, $result, true)) {
								// There are some errors in the plugins
								JError::raiseWarning(100, JText::plural('COM_DOCUMENT_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
								continue;
							}

							$ret &= JFolder::delete($fullPath);

							// Trigger the onContentAfterDelete event.
							$dispatcher->trigger('onContentAfterDelete', array('com_document.folder', &$object_file));
							$this->setMessage(JText::sprintf('COM_DOCUMENT_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
						}
						else
						{
							//This makes no sense...
							JError::raiseWarning(100, JText::sprintf('COM_DOCUMENT_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY',substr($fullPath, strlen(COM_MEDIA_BASE))));
						}
					}
				}
			}
			return $ret;
		}
	}
	
	function featured()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('featured' => 1, 'unfeatured' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_document.document.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_document&view=documents');
	}
}
