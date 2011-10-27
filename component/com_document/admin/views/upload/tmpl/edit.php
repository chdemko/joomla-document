<?php 

/**
 * @version		$Id: edit.php 169 2010-12-17 14:47:11Z crazy_pedro $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_document&task=document.upload'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="width-70 fltlft">
		<fieldset id="uploadform">
		<legend><?php echo JText::_('COM_DOCUMENT_UPLOAD'); ?></legend>
			<label for="document_directory"><?php echo JText::_('COM_DOCUMENT_UPLOAD_DIRECTORY'); ?>&nbsp;&nbsp;</label>			
			<input class="input_box" id="document_directory" name="document_directory" type="file" size="57" />
			<input class="button" type="submit" value="<?php echo JText::_('COM_DOCUMENT_UPLOAD_DOCUMENT'); ?>" />
	</fieldset>
	</div>
</form>
