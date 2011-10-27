<?php

/**
 * @version		$Id: upload.php 125 2010-12-17 08:58:44Z helvys $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modelform');

/**
 * Upload Model of Document component
 */
class DocumentModelUpload extends JModelForm
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */

	protected function populateState()
	{
		$file	= JRequest::getVar('Filedata', '', 'files', 'array');
		$this->setState('document.filedata', $file);
	}
 
	public function upload()
	{
		$file	= $this->getState('document.filedata');

		if ( ! JFile::upload( $file, getVar('JPATH_ROOT').'/image/pdf' ))
		{
			// Error in upload
			JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
			return false;
		}
		else
		{
			// Trigger the onContentAfterSave event.
			// $dispatcher->trigger('onDocumentAfterSave', array('com_media.file', &$object_file));
			// $this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
			return true;
		}
	}
	
	
	public function getTable($type = 'Document', $prefix = 'DocumentTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_document.upload', 'upload', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
}
