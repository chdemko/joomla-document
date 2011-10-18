<?php

/**
* @version        $Id$
* @package        Document
* @subpackage    Component
* @copyright    Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
* @link        http://joomlacode.org/gf/project/document/
* @license        http://www.gnu.org/licenses/gpl-2.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modeladmin library
jimport('joomla.application.component.modeladmin');

/**
* Document Model of Document component
*/
class DocumentModelDocument extends JModelAdmin
{
  /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param    type    The table type to instantiate
    * @param    string    A prefix for the table class name. Optional.
    * @param    array    Configuration array for model. Optional.
    * @return    JTable    A database object
    * @since    1.6
    */
  

public function getTable($type = 'Document', $prefix = 'DocumentTable', $config = array())
  {
      return JTable::getInstance($type, $prefix, $config);
  }
  /**
    * Method to get the record form.
    *
    * @param    array    $data        Data for the form.
    * @param    boolean    $loadData    True if the form is to load its own data (default case), false if not.
    * @return    mixed    A JForm object on success, false on failure
    * @since    1.6
    */
  public function getForm($data = array(), $loadData = true)
  {
      // Get the form.
      $form = $this->loadForm('com_document.document', 'document', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form))
      {
          return false;
      }
      return $form;
  }


//Returns information about a specific file - Retourne les donn�es d�un fichier sp�cifique
public function getItem()
    {
      // Create a new query object.
      $db = JFactory::getDBO();
      $query = $db->getQuery(true);
          $id = JRequest::getInt('id');

    // Select some fields
$query->select('id,title,keywords,description,author,alias,filename,mime,catid,created,created_by,created_by_alias,modified,modified_by,hits,params,language,featured,ordering,published,publish_up,publish_down,access,checked_out,checked_out_time');

         // From the document table
         $query->from('#__document');

$query->where('id ='.(int)$id);



     return $query;
    }

	/**
	 * Method to change the featured state of one or more records.
	 *
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the featured state.
	 *
	 * @return  boolean  True on success.
	 * @since   11.1
	 */
	function feature(&$pks, $value = 1)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin('content');

		// Access checks.
		foreach ($pks as $i => $pk) {
			$table->reset();

			if ($table->load($pk)) {
				if (!$this->canEditState($table)) {
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->feature($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		$context = $this->option.'.'.$this->name;

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}


}