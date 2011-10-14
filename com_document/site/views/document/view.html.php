<?php

/**
 * @version		$Id: view.html.php 120 2010-12-17 08:01:57Z hammihicham $
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
 * Document View of Document component
 */
class DocumentViewDocument extends JView
{
	/**
	 * Document view display method
	 */
	function display( $tpl = null )
{
  // Get the model  
  $model =&$this->getModel();
 
    // Get all of the items
    $item = $model->getItem();
    $this->assignRef( 'item', $item );
  
  
  // Display the view
  parent::display( $tpl );
}
}