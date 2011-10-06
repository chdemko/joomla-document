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
	 * Upload view display method
	 */
	public function display($tpl = null) 
	{	
		if (!JFactory::getUser()->authorize('core.create','com_document')) {
			JRaiseError(403, 'comment');
			return false;
		}
		

		// Display the template
		parent::display($tpl);
	}
}
