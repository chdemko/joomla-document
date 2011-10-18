<?php

/**
 * @version		$Id: documents.php 142 2010-12-17 12:27:20Z crazy_pedro $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Documents Controller of Document component
 */
class DocumentControllerDocuments extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   0.0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('unfeature',	'feature');	// value = 0
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Document', $prefix = 'DocumentModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	
	protected function add($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_document.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			$msg = "Vous n'avez pas le droit de créer un document.";
			return $msg;
		}
		else {
			$this->setRedirect('index.php?option=com_document&view=upload&layout=edit');
			return $allow;
		}
	}

	function feature()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('feature' => 1, 'unfeature' => 0);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Feature the items.
			if (!$model->feature($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
			else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_FEATURED';
				}
				elseif ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_UNFEATURED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=com_document', false));
	}
}

