<?php
/**
 * @version		$Id: mod_documents_category.php 171 2010-12-17 14:49:48Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

// Include the helper functions only once
require_once dirname(__FILE__).DS.'helper.php';

		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		$idbase = null;
		switch($mode)
		{
			case 'dynamic':
				$option = JRequest::getCmd('option');
				$view = JRequest::getCmd('view');
				if ($option === 'com_content') {
					switch($view)
					{
						case 'category':
							$idbase = JRequest::getInt('id');
							break;
						case 'categories':
							$idbase = JRequest::getInt('id');
							break;
						case 'document':
							if ($params->get('show_on_document_page', 1)) {
								$idbase = JRequest::getInt('catid');
							}
							break;
					}
				}
				break;
			case 'normal':
			default:
				$idbase = $params->get('catid');
				break;
		}



$cacheid = md5(serialize(array ($idbase,$module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'modDocumentsCategoryHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);


if (!empty($list)) {
	$grouped = false;
	$document_grouping = $params->get('document_grouping', 'none');
	$document_grouping_direction = $params->get('document_grouping_direction', 'ksort');
	if ($document_grouping !== 'none') {
		$grouped = true;
		switch($document_grouping)
		{
			case 'year':
			case 'month_year':
				$list = modDocumentsCategoryHelper::groupByDate($list, $document_grouping, $document_grouping_direction, $params->get('month_year_format', 'F Y'));
				break;
			case 'author_name':
			case 'category_title':
				$list = modDocumentsCategoryHelper::groupBy($list, $document_grouping, $document_grouping_direction);
				break;
			default:
				break;
		}
	}
    require JModuleHelper::getLayoutPath('mod_documents_category', $params->get('layout', 'default'));
}
