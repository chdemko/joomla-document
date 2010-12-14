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

JLoader::register('modDocumentsArchiveHelper', dirname(__FILE__) . DS . 'helper.php');
// Include the syndicate functions only once
//require_once dirname(__FILE__).DS.'helper.php';

$params->def('count', 10);
$list = modDocumentsArchiveHelper::getList($params);

require JModuleHelper::getLayoutPath('mod_documents_archive', $params->get('layout', 'default'));
