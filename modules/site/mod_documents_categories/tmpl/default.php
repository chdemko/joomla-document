<?php
/**
 * @version		$Id: default.php 172 2010-12-17 14:50:19Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;
?>
<ul class="categories-module<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
require JModuleHelper::getLayoutPath('mod_documents_categories', $params->get('layout', 'default').'_items');
?></ul>

