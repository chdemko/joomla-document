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
	function onContentSearchAreas()
	{
		return array();
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

		echo "Recherche OK";
		exit();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);


		$query_title = 'SELECT title FROM #__document';


		$db->setQuery($query_title);
		$result = $db->loadResult();

		echo $result;


		return array();
	}
}

