<?php

/**
 * @version		$Id: category.php 160 2010-12-17 13:44:54Z aguibe01 $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

jimport('joomla.application.categories');

/**
 * Content Component Category Tree
 *
 * @static
 * @package		Joomla
 * @subpackage	com_content
 * @since 1.6
 */
class DocumentCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__document';
		$options['extension'] = 'com_document';
		parent::__construct($options);
	}
}


