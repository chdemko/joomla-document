<?php

/**
 * @version		$Id: default.php 97 2010-12-15 12:09:35Z tbonnaud $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_document&view=versions&layout=edit&id='.$this->item->id);?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_DOCUMENT_GRID_HEADING_VERSION', 'a.number', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JDEFAULT', 'isdefault', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DOCUMENT_GRID_HEADING_CREATED_BY', 'ua.name', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'COM_DOCUMENT_GRID_HEADING_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DOCUMENT_GRID_HEADING_MODIFIED_BY', 'um.name', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'COM_DOCUMENT_GRID_HEADING_MODIFIED_DATE', 'a.modified', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if ($this->items): foreach ($this->items as $i => $item) :?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Versions.checkbox', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="right">
					<?php echo JHtml::_('DocumentHtml.Versions.number', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Versions.isdefault', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td>
					<?php echo JHtml::_('DocumentHtml.Versions.createdBy', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('DocumentHtml.Versions.creationDate', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td>
					<?php echo JHtml::_('DocumentHtml.Versions.modifiedBy', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('DocumentHtml.Versions.modificationDate', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
			</tr>
			<?php endforeach; endif;?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
