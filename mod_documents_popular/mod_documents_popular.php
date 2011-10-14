<?php
/**
 * @version		$Id: mod_documents_popular.php 132 2010-12-17 09:41:02Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$list = modDocumentsPopularHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_documents_popular', $params->get('layout', 'default'));
