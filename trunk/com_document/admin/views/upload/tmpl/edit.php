<?php

/**
 * @version		$Id$
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="width-70 fltlft">
	<fieldset id="uploadform">
	<legend><?php echo JText::_('COM_DOCUMENT_UPLOAD'); ?></legend>
	<label for="install_package"><?php echo JText::_('COM_DOCUMENT_UPLOAD_DOCUMENT'); ?></label>
		<form action="<?php echo JRoute::_('index.php?option=com_document&view=upload');?>" method="post" action="xxxxxxxxxxxxxx	" enctype="multipart/form-data">   
			<input type="hidden" name="MAX_FILE_SIZE" value="2097152"></td>   
			<input type="file" name="file_name">  
			<input type="submit" value="Upload document">  
		</form>
	</fieldset>
</div>
<?php