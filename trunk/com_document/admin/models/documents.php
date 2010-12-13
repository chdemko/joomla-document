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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
* Documents Model of Document component
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
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

    // Select some fields
          $query->select('id,title,keywords,description,author,alias,filename,mime,catid,created,created_by,created_by_alias,modified,modified_by,hits,params,language,featured,ordering,published,publish_up,publish_down,access,checked_out,checked_out_time');

           // From the document table
           $query->from('#__document');


        return $query;
    }
}