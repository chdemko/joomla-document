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
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_document')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
JLoader::register('documentHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'document.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by document
$controller = JController::getInstance('document');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
