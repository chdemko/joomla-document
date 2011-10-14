<?php

/**
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * Documents Model of Document component
 * 
 * @package		Document
 * @subpackage	Component
 * @since		0.0.1
 */
class DocumentModelDocuments extends JModelList
{
    /**
    * Method to build an SQL query to load the list data.
    *
    * @return    JDatabaseQuery    An SQL query
    */
    protected function getListQuery()
    {
    	$query = parent::getListQuery();
    	$query->select('*');
    	$query->from('#__document');
    	return $query;
    }
}
