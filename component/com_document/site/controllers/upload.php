<?php

/**
 * @version		$Id: upload.php 151 2010-12-17 13:27:39Z eexit $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controllerform');

/**
 * Upload Controller of Document component
 */
class DocumentControllerUpload extends JControllerForm
{
    /**
	 * @since	1.6
	 */
	protected $view_item = 'form';

	/**
	 * @since	1.6
	 */
	protected $view_list = 'categories';

	/**
	 * Constructor
	 *
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('catid'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_document.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}
	
	protected function setReturnPage()
	{
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";

		$return = JRequest::getVar('return', null, 'default', 'base64');

		$app->setUserState($context.'.return', $return);
	}
	
    /**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the article can be added, false if not.
	 * @since	1.6
	 */
	public function add()
	{
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";

		// Access check
		if (!$this->allowAdd()) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.data', null);
		
        // if ($this->getModel()->upload()) {
        if (false) {
            // Clear the return page.
    		// TODO: We should be including an optional 'return' variable in the URL.
    		$this->setReturnPage();

    		// ItemID required on redirect for correct Template Style
    		$redirect = 'index.php?option=com_document&view=form&layout=edit';
    		if (JRequest::getInt('Itemid') != 0) {
    			$redirect .= '&Itemid='.JRequest::getInt('Itemid');
    		}

    		$this->setRedirect($redirect);
        } else {
            // Clear the return page.
    		// TODO: We should be including an optional 'return' variable in the URL.
    		$this->setReturnPage();

    		// ItemID required on redirect for correct Template Style
    		$redirect = 'index.php?option=com_document&view=form&layout=edit';
    		if (JRequest::getInt('Itemid') != 0) {
    			$redirect .= '&Itemid='.JRequest::getInt('Itemid');
    		}
            
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $this->getModel()->getError());
    		$this->setRedirect($redirect, $message, 'error');
        }
        
		return true;
	}
}
