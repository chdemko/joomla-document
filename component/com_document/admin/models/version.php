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

// import Joomla modeladmin library
jimport('joomla.application.component.modeladmin');

/**
 * Version Model of Document component
 *
 * @package		Document
 * @subpackage	Component
 * @since       0.0.1
 */
class DocumentModelVersion extends JModelAdmin
{
	/**
	 * The version item.
	 *
	 * @var    object
	 * @since  0.0.1
	 */
	protected $item;

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object   $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 * @since   0.0.1
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
		$document = $this->getTable('Document');
		if (!$document->load($record->document_id))
		{
			return false;
		}
		if ($document->version == $record->number)
		{
			return false;	
		}
		return $user->authorise('core.delete', $this->option);
	}

  /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param    type    The table type to instantiate
    * @param    string  A prefix for the table class name. Optional.
    * @param    array   Configuration array for model. Optional.
    * @return   JTable  A database object
    * @since    0.0.1
    */
	public function getTable($type = 'Version', $prefix = 'DocumentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 * @since   0.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_document.version', 'version', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 * @since   0.0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_document.edit.version.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 * @since   0.0.1
	 */
	public function getItem($pk = null)
	{
		if (!isset($this->item))
		{
			if ($this->item = parent::getItem($pk))
			{
				$document = $this->getTable('Document');
				if ($document->load($this->item->document_id))
				{
					$this->item->title = $document->title;
					$this->item->alias = $document->alias;
					$this->item->featured = $document->featured;
					$this->item->published = $document->published;
					$this->item->language = $document->language;
					$this->item->checked_out = $document->checked_out;
					$this->item->checked_out_time = $document->checked_out_time;
				}
				else
				{
					$this->item = false;
				}
			}		
		}
		return $this->item;
	}
}
