<?php

/**
 * @version		$Id: view.raw.php 94 2010-12-15 12:01:54Z tbonnaud $
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
jimport('joomla.filesystem.file');

/**
 * Download View of Document component
 */
class DocumentViewDownload extends JView
{
	/**
	 * Documents view display method
	 */
	function display($tpl = null) 
	{
		$item	= $this->get('item');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("<br />", $errors));
			return false;
		}
		if (!in_array($item->access,JFactory::getUser()->getAuthorisedViewLevels()))
		{
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
			return false;
		}
		if(!JFile::exists($item->filename))
		{
			JError::raiseError(404, JText::_('COM_DOCUMENT_ERROR_DOCUMENT_NOT_FOUND'));
			return false;
		}
		
		$document = JFactory::getDocument();
		$document->setMimeEncoding($item->mime);
		JResponse::setHeader('Content-disposition', 'attachment; filename="'.$item->title.'.'.JFile::getExt($item->filename).'"; creation-date="'.JFactory::getDate()->toRFC822().'"', true);
		echo file_get_contents(JPATH_ROOT . '/' . $item->filename);
	}
}
