<?php

/**
 * @version		$Id: default.php 148 2010-12-17 13:10:19Z marcetienne $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

	<form method="POST" action="<?php echo JRoute::_('index.php?option=com_document&task=document.upload'); ?>" enctype="multipart/form-data">
	     <input type="hidden" name="MAX_FILE_SIZE" value="31457280">
	     Fichier &agrave; d&eacute;poser : <input type="file" name="fichier">
				<p><p/>
	     <input type="submit" name="envoyer" value="Envoyer le fichier">
	</form>












