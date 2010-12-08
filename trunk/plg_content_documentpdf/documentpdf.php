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

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILENAME__));

require_once 'Zend/Pdf.php';

jimport('joomla.plugin.plugin');

/**
 * PDF Document Content Plugin
 */
class plgContentDocumentPDF extends JPlugin
{
	public function onContentAfterDelete($context, &$media)
	{
		return true;
	}

	public function onContentAfterSave($context, &$media)
	{
		return true;
	}

	public function onContentBeforeDelete($context, &$media)
	{
		return true;
	}

	public function onContentBeforeSave($context, &$media)
	{
		return true;
	}

	public function onContentAfterDisplay($context, &$media)
	{
	}

	public function onContentBeforeDisplay($context, &$media)
	{
	}
}

