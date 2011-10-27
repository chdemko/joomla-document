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

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_document');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">

		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_DOCUMENT_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_document'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<select name="filter_author_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_DOCUMENT_OPTION_SELECT_CREATOR');?></option>
				<?php // TODO echo JHtml::_('select.options', $this->creators, 'value', 'text', $this->state->get('filter.author_id'));?>
			</select>

			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('JCATEGORY'); ?>
				</th>
				<th width="7%">
					<?php echo JText::_('COM_DOCUMENT_GRID_HEADING_VERSION'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($listOrder == 'ordering') :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'documents.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'ag.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'ua.name', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'l.title', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
					<?php echo JHtml::_('DocumentHtml.Documents.checkbox', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td>
					<?php echo JHtml::_('DocumentHtml.Documents.title', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.published', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.featured', $i, $item, $this, $listOrder, $listDirn); ?>
				</td>
				<td>
					<?php echo JHtml::_('DocumentHtml.Documents.categories', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td>
					<?php echo JHtml::_('DocumentHtml.Documents.version', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="order">
					<?php echo JHtml::_('DocumentHtml.Documents.order', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.access', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.creator', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('DocumentHtml.Documents.date', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.hits', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.language', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.id', $i, $item, $this, $listOrder, $listDirn);?>
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
