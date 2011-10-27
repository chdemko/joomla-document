<?php
/**
 * @version		$Id: mod_documents_latest.php 126 2010-12-17 09:14:28Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$list = modDocumentsLatestHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_documents_latest', $params->get('layout', 'default'));
