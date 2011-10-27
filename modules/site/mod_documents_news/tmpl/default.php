<?php
/**
 * @version		$Id: default.php 173 2010-12-17 14:51:57Z aguibe01 $
 * @package		Documents
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="newsflash<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :?>
	<?php
	 require JModuleHelper::getLayoutPath('mod_documents_news', '_item');?>
<?php endforeach; ?>
</div>

