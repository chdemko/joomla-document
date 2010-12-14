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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Upload Controller of Document component
 */
class DocumentControllerUpload extends JController
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
		$app->setUserState($context.'.data',	null);

		// Clear the return page.
		// TODO: We should be including an optional 'return' variable in the URL.
		$this->setReturnPage();

		// ItemID required on redirect for correct Template Style
		$redirect = 'index.php?option=com_document&view=form&layout=edit';
		if (JRequest::getInt('Itemid') != 0) {
			$redirect .= '&Itemid='.JRequest::getInt('Itemid');
		}

		$this->setRedirect($redirect);

		return true;
	}
}
