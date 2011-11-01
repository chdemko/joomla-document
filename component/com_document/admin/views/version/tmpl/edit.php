<?php

/**
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - 2011 Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_document&view=version&layout=edit&id='.(int) $this->item->version->id); ?>" method="post" name="adminForm" id="module-form" class="form-validate">
	<div class="width-60 fltlft">
		<?php echo JHtml::_('DocumentHtml.Version.fieldsets', 'version', $this); ?>
		<?php echo JHtml::_('DocumentHtml.Version.fieldsets', 'document', $this); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'version-sliders'); ?>
		<?php echo JHtml::_('DocumentHtml.Version.sliders', 'params', $this); ?>
		<?php echo JHtml::_('DocumentHtml.Version.sliders', 'attribs', $this); ?>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
