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

JLoader::register('modDocumentsCategoryHelper', dirname(__FILE__) . DS . 'helper.php');

require JModuleHelper::getLayoutPath('mod_documents_category', $params->get('layout', 'default'));
