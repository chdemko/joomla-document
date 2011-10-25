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

$user		= JFactory::getUser();
$userId		= $user->get('id');
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
				<option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR');?></option>
				<?php echo JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'));?>
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
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JFEATURED', 'featured', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('JCATEGORY'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($listOrder == 'ordering') :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'documents.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'author', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'created', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
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
				<td class="order">
					<?php echo JHtml::_('DocumentHtml.Documents.order', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.access', $i, $item, $this, $listOrder, $listDirn);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('DocumentHtml.Documents.author', $i, $item, $this, $listOrder, $listDirn);?>
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
