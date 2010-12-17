<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).DS.'helper.php';

$list = modDocumentsCategoriesHelper::getList($params);
if (!empty($list)) {
	$startLevel = reset($list)->getParent()->level;
	require JModuleHelper::getLayoutPath('mod_documents_categories', $params->get('layout', 'default'));
}

