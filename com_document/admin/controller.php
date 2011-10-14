<?php

/**
 * @version		$Id: controller.php 80 2010-12-15 09:08:42Z crazy_pedro $
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
 * General Controller of Document component
 */
class DocumentController extends JController
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $default_view = 'documents';
}
