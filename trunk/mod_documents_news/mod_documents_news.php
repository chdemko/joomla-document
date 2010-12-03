<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JLoader::register('modDocumentsNewsHelper', dirname(__FILE__) . DS . 'helper.php');

require JModuleHelper::getLayoutPath('mod_documents_news', $params->get('layout', 'default'));
