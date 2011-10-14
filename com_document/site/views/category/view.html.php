<?php

/**
 * @version		$Id: view.html.php 124 2010-12-17 08:56:56Z eexit $
 * @package		Document
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @link		http://joomlacode.org/gf/project/document/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Category View of Document component
 */
class DocumentViewCategory extends JView
{
	/**
	 * Category view display method
	 */
	public function display($tpl = null) 
	{
		try
		{
		    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		    $bdd = new PDO('mysql:host=localhost;dbname=SQL#__document', 'root', '', $pdo_options);
		    
		    $req = $bdd->prepare('SELECT ..... id and nom....');
		    $req->execute(array('id' => $_GET['id article ?'], 'nom' => $_GET['nom article ?']));
		    
		    echo '<ul>';
		    while ($donnees = $req->fetch())
		    {
			    echo '<li>' . $donnees['nom'] . ' (' . $donnees['prix'] . ' EUR)</li>';
		    }
		    echo '</ul>';
		    
		    $reponse->closeCursor();
		}
		catch(Exception $e)
		{
		    die('Erreur : '.$e->getMessage());
		}
		
//		$this->msg = $rqt;
		
		
		
		// Display the template
		parent::display($tpl);
	}
}
