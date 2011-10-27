<?php

/**
* @version        $Id$
* @package        Document
* @subpackage    Component
* @copyright    Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
* @link        http://joomlacode.org/gf/project/document/
* @license        http://www.gnu.org/licenses/gpl-2.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modeladmin library
jimport('joomla.application.component.modeladmin');

/**
 * Version Model of Document component
 */
class DocumentModelVersion extends JModelAdmin
{
  /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param    type    The table type to instantiate
    * @param    string  A prefix for the table class name. Optional.
    * @param    array   Configuration array for model. Optional.
    * @return   JTable  A database object
    * @since    0.0.1
    */
	public function getTable($type = 'Version', $prefix = 'DocumentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}