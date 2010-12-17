<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Plugins
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


/**
 * Document Search Plugin
 */
class plgSearchDocument extends JPlugin
{
	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas(){
		static $areas = array(
				'plg_search_document' => 'Document'
        );
        return $areas;

	}

	/**
	 * Content Search method
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db = JFactory::getDBO();
	
		//Return Array when nothing was filled in.
		if ($text == '') {
                return array();
        }
		
		//test si l'on effectue les test
		if(is_array($areas) && !is_null($areas) && !in_array('plg_search_document', $areas))
		{
			return array();
		}
		
	
	
		//élaboration de la recherche
        $wheres = array();
        switch ($phrase) {
			//search exact
                case 'exact':
                        $text        = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
                        $wheres2     = array();
                        $wheres2[]   = 'LOWER(d.title) LIKE '.$text.' OR LOWER(d.keywords) LIKE '.$text.' OR LOWER(d.description) LIKE '.$text.' OR LOWER(d.author) LIKE '.$text.' OR LOWER(d.filename) LIKE '.$text.' OR LOWER(s.field_value) LIKE '.$text;
                        $where                 = '(' . implode( ') OR (', $wheres2 ) . ')';
                        break;
 
			//search all
                case 'all':
						$words         = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word)
                        {
                            $word          = $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                            $wheres2       = array();
                            $wheres2[]   = '(LOWER(d.title) LIKE '.$word.' OR LOWER(d.keywords) LIKE '.$word.' OR LOWER(d.description) LIKE '.$word.' OR LOWER(d.author) LIKE '.$word.' OR LOWER(d.filename) LIKE '.$word.' OR LOWER(s.field_value) LIKE '.$word.')';
                            $wheres[]    = implode( ' AND ', $wheres2 );
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
				
				
			//search any
		   case 'any':
			//set default
                default:
                        $words         = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word)
                        {
                                $word          = $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                                $wheres2       = array();
                                $wheres2[]   = '(LOWER(d.title) LIKE '.$word.' OR LOWER(d.keywords) LIKE '.$word.' OR LOWER(d.description) LIKE '.$word.' OR LOWER(d.author) LIKE '.$word.' OR LOWER(d.filename) LIKE '.$word.' OR LOWER(s.field_value) LIKE '.$word.')';
                                $wheres[]    = implode( ' OR ', $wheres2 );
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }
 

		//Try selon l'ordre souhaité
        switch ( $ordering ) {
		
			//oldest first
                case 'oldest':
						$order = 'd.created ASC';
						break;
			//popular first
                case 'popular':
						$order = 'd.hits DESC';
						break;
			//newest first
                case 'newest':
						$order = 'd.created DESC';
						break;
			//alphabetic, ascending
                case 'alpha':
			//default setting: alphabetic, ascending
                default:
						$order = 'd.title ASC';
                        break;
        }

		
		//replace nameofplugin
        $searchNameofplugin = JText::_( 'plg_search_document' );
		
		$query = 'SELECT d.id, d.title AS title, d.description AS text, d.created AS created'
        . ' FROM #__document d, #__document_fields s'
        . ' WHERE ( '. $where .' ) AND d.id = s.id'
		. ' AND d.published = 1'
		. ' ORDER BY '.$order
		;

		//Set query
        $db->setQuery( $query );
		$db->query();
		
        $rows = $db->loadObjectList();
	 
		if (is_array($rows))
		{
	        foreach($rows as $key => $row) {
	            $rows[$key]->href = 'index.php?option=com_document&view=document&id='.$rows[$key]->id;
				$rows[$key]->section = 'pdf_section';
				$rows[$key]->browsernav = '1';
	        }
		}
		return $rows;
	}
	
	
	
}

