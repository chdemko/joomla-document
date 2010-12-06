<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JLoader::register('modDocumentsLatestHelper', dirname(__FILE__) . DS . 'helper.php');

require JModuleHelper::getLayoutPath('mod_documents_latest', $params->get('layout', 'default'));
