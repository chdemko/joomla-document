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

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Document Controller of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
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

/*	public function upload()
	{
		var_dump(COM_DOCUMENT_BASE);
		exit ();
		//JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user = JFactorygetUser();

		// Get some data from the request
		// FileName, FileSize
		$file = JRequestgetVar('filedata', '', 'files', 'array');
		// Folder on the server
		$folder = JRequestgetVar('folder', '', '', 'path');
		$return = JRequestgetVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelpersetCredentialsFromRequest('ftp');

		// Set the redirect
		if ($return)
		{
			$this->setRedirect(base64_decode($return) . '&folder=' . $folder);
		}

		// Make the filename safe
		$file['name'] = JFilemakeSafe($file['name']);

		if (isset ($file['name']))
		{
			// The request is valid
			$err = null;
			if (!MediaHelpercanUpload($file, $err))
			{
				// The file can't be upload
				JErrorraiseNotice(100, JText_($err));
				return false;
			}

			$filepath = JPathclean(COM_DOCUMENT_BASE . DS . $folder . DS . strtolower($file['name']));

			// Trigger the onContentBeforeSave event.
			JPluginHelperimportPlugin('document');
			$dispatcher = JDispatchergetInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array (
				'com_document.file',
				& $object_file
			));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				JErrorraiseWarning(100, JTextplural('COM_DOCUMENT_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			$file = (array) $object_file;

			if (JFileexists($file['filepath']))
			{
				// File exists
				JErrorraiseWarning(100, JText_('COM_DOCUMENT_ERROR_FILE_EXISTS'));
				return false;
			}

			if (!$user->authorise('core.create', 'com_document'))
			{
				// File does not exist and user is not authorised to create
				JErrorraiseWarning(403, JText_('COM_DOCUMENT_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}
			else
			{

			}

			if (!JFileupload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JErrorraiseWarning(100, JText_('COM_DOCUMENT_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array (
					'com_document.file',
					& $object_file
				));
				$this->setMessage(JTextsprintf('COM_DOCUMENT_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_DOCUMENT_BASE))));
				return true;
			}
		}
		else
		{
			$this->setRedirect('index.php', JText_('COM_DOCUMENT_INVALID_REQUEST'), 'error');
			return false;
		}

	}
*/
}