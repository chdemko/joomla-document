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
	 * Method to check-out a row for editing.
	 *
	 * @param   integer  $pk	The numeric id of the primary key.
	 *
	 * @return  boolean	False on failure or error, true otherwise.
	 * @since   0.0.1
	 */
	public function checkout($pk = null)
	{
		// Only attempt to check the row in if it exists.
		if ($pk) {
			$user = JFactory::getUser();

			// Get an instance of the row to checkout.
			$table = $this->getTable();
			if (!$table->load($pk)) {
				$this->setError($table->getError());
				return false;
			}

			$document = $this->getTable('Document');
			if (!$document->load($table->document_id)) {
				$this->setError($document->getError());
				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($document->checked_out > 0 && $document->checked_out != $user->get('id')) {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH'));
				return false;
			}

			// Attempt to check the row out.
			if (!$document->checkout($user->get('id'), $table->document_id)) {
				$this->setError($document->getError());
				return false;
			}
		}

		return true;
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
			if ($version = parent::getItem($pk))
			{
				$document = $this->getTable('Document');
				if ($document->load($version->document_id))
				{
					$query = $this->_db->getQuery(true);
					$query->select('category_id');
					$query->from('#__document_category_map');
					$query->where('document_id = ' . (int) $version->document_id);
					$this->_db->setQuery($query);
					$document->catid = $this->_db->loadColumn();
					$this->item = (object) array(
						'version' => (object) $version->getProperties(),
						'document' => (object) $document->getProperties(),
						'params' => $version->params
					);
				}
				else
				{
					$this->item = false;
				}
			}
		}
		return $this->item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 * @since   0.0.1
	 */
	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			$table = $this->getTable();
			$pk = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');

			// Load the row
			if (!$table->load($pk))
			{
				$this->setError(JText::_('COM_DOCUMENT_ERROR_UNEXISTING_ID'));
				return false;
			}

			// Bind the data.
			$version = $data['version'];
			$version['params'] = empty($data['params']) ? null : $data['params'];
			if (!$table->bind($version))
			{
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, false));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, false));

			$this->setState($this->getName().'.id', $table->id);
	
			$document = $this->getTable('Document');

			// Load the row
			if (!$document->load($table->document_id))
			{
				$this->setError(JText::_('COM_DOCUMENT_ERROR_UNEXISTING_ID'));
				return false;
			}

			// Bind the data.
			if (!$document->bind($data['document']))
			{
				$this->setError($document->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($document);

			// Check the data.
			if (!$document->check())
			{
				$this->setError($document->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$document, false));
			if (in_array(false, $result, true))
			{
				$this->setError($document->getError());
				return false;
			}

			// Store the data.
			if (!$document->store())
			{
				$this->setError($document->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$document, false));

			// Deal with categories
			$query = $this->_db->getQuery(true);
			$query->delete();
			$query->from('#__document_category_map');
			$query->where('document_id = ' . (int) $pk);
			$this->_db->setQuery($query);
			$this->_db->query();

			$query = $this->_db->getQuery(true);
			$query->insert('#__document_category_map');
			foreach ($data['document']['catid'] as $catid)
			{
				$query->values($pk . ',' . $catid);
			}
			$this->_db->setQuery($query);
			$this->_db->query();

			$this->setState($this->getName().'.id', $pk);

			return true;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}
}
