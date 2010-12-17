<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Plugins
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

//     cd /home/walien/eclipse/workspaces/workspace_u/com_document/plg_search_document

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
	function onContentSearchAreas()
	{
		static $areas = array(
                'plg_search_document' => 'Documents'
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
		$plgName = "plg_search_document";

		//Return Array when nothing was filled in
		if ($text == '') {
			return array();
		}

		//Checking if the user checks the Documents tickbox
		if(is_array($areas) && !in_array($plgName, $areas) && !is_null($areas)) {
			return array();
		}

		$wheres = array();
		switch ($phrase) {
			//search exact
	 	case 'exact':
	 		$text        = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
	 		$wheres2     = array();
	 		$wheres2[]   = 'LOWER(d.title) LIKE '.$text.' OR LOWER(d.keywords) LIKE '.$text.' OR LOWER(d.description) LIKE '.$text.' OR LOWER(d.author) LIKE '.$text.' OR LOWER(d.filename) LIKE '.$text;
	 		$where       = '(' . implode( ') OR (', $wheres2 ) . ')';

	 		$where_extra= 'LOWER(df.field_value) LIKE '.$text.' ';

	 		break;
	 		//search all
	 	case 'all':
	 		$words         = explode( ' ', $text );
	 		$wheres = array();
	 		foreach ($words as $word)
	 		{
	 			$word        = $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
	 			$wheres2     = array();
	 			$wheres2[]   = '(LOWER(d.title) LIKE '.$word.' OR LOWER(d.keywords) LIKE '.$word.' OR LOWER(d.description) LIKE '.$word.' OR LOWER(d.author) LIKE '.$word.' OR LOWER(d.filename) LIKE '.$word.')';

	 			$where_field = array();
	 			$where_field[] = 'LOWER(df.field_value) LIKE '.$word.' ';

	 			$wheres[]    = implode( ' AND ', $wheres2 );
	 			$wheres_field[] = implode(' AND ', $where_field);

	 		}
	 		$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
	 		$where_extra =  '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres_field ) . ')';
	 		break;
	 		//search any
	 	case 'any':
	 		//set default
	 	default:
	 		$words         = explode( ' ', $text );
	 		$wheres = array();
	 		foreach ($words as $word)
	 		{
	 			$word      = $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
	 			$wheres2   = array();

	 			$wheres2[] = '(LOWER(d.title) LIKE '.$word.' OR LOWER(d.keywords) LIKE '.$word.' OR LOWER(d.description) LIKE '.$word.' OR LOWER(d.author) LIKE '.$word.' OR LOWER(d.filename) LIKE '.$word.')';

	 			$where_field = array();
	 			$where_field[] = 'LOWER(df.field_value) LIKE '.$word.' ';

	 			$wheres[]  = implode( ' OR ', $wheres2 );
	 			$wheres_field[] = implode(' OR ', $where_field);
	 		}
	 		$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
	 		$where_extra =  '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres_field ) . ')';
	 		break;
	 }

		//ordering of the results
	 switch ( $ordering ) {
			//oldest first
	 	case 'oldest':
	 		$order = 'nt.created ASC';
	 		break;
	 		//popular first
	 	case 'popular':
	 		$order = 'nt.hits DESC';
	 		break;
	 		//newest first
	 	case 'newest':
	 		$order = 'nt.created DESC';
	 		break;
	 		//alphabetic, ascending
	 	case 'alpha':
	 		//default setting: alphabetic, ascending
	 	default:
	 		$order = 'nt.title ASC';
	 		break;
	 }

		$query = 'SELECT nt.title AS title, nt.description AS text, nt.created AS created, nt.mime AS section'
		. ' FROM ('
		. ' SELECT DISTINCT d.title, d.description, d.created, d.mime'
		. ' FROM #__document d'
		. ' WHERE ( '. $where .' )'
		. ' AND d.published = 1'
		. ' UNION '
		.'SELECT DISTINCT d.title, d.description, d.created, d.mime'
		. ' FROM #__document d, #__document_fields df'
		. ' WHERE ( '.$where_extra.' )'
		. ' AND d.id = df.id'
		. ') AS nt'
		. ' ORDER BY '.$order
		;

		//Set query
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (is_array($rows))
		{
			foreach($rows as $key => $row) {
				$rows[$key]->href = 'index.php?option=com_document&view=document&id='.$rows[$key]->id;
				$rows[$key]->browsernav= '1 ';
			}
		}
		return $rows;
	}
}