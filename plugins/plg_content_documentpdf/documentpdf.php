<?php
/**
 * @version		$Id: documentpdf.php 161 2010-12-17 13:47:49Z eoriou $
 * @package		Documents
 * @subpackage	Plugins
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */


// No direct access
defined('_JEXEC') or die;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILENAME__));

require_once 'Zend/Pdf.php';

jimport('joomla.plugin.plugin');

/**
 *
 * When a user uploads a PDF file on the JOOMLA server via the Media Manager Component,
 * this plugin retrieves informations about the file (metadatas) and adds them to the
 * Database in order to be indexed.
 *
 * @author Tony FAUCHER, Elian ORIOU, Etienne RAGONNEAU
 *
 */

class plgContentDocumentPDF extends JPlugin
{

	/**
	 * Function called after a content deletion
	 * @param string $context The component which launch the event 'ContentAfterDelete'
	 * @param array $media The array that contains informations concerning the element which was deleted
	 */

	public function onContentAfterDelete($context, &$media)
	{
		$app = JFactory::getApplication();

		/*Filtering event sources (only events launched by the media manager component or by the document component)*/
		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/*Replaces paths separators (depending of the execution environment)*/
		$filename = str_replace('\\', '\\\\', $media->filepath);

		/*The deletion query*/
		$query = "DELETE FROM  #__document WHERE filename = '$filename'";

		$db->setQuery($query);
		$db->query();
		return true;
	}

	/**
	 * Function called after a content save
	 * @param string $context The component which launch the event 'ContentAfterSave'
	 * @param array $media The array that contains informations concerning the element which was saved
	 */

	public function onContentAfterSave($context, &$media)
	{
		$app = JFactory::getApplication();

		/*Filtering event sources (only events launched by the media manager component or by the document component)*/
		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}

		$component_params = JComponentHelper::getParams('com_content');
		/*Check if the file is downloaded into the 'Documents' (placed in 'JOOMLA_PATH/images/Documents') folder*/
		if(strpos(JPATH_BASE, '/')>=0) {
			$path = explode("/", JPATH_BASE);
			$path[count($path)-1] = '';
			$path = implode('\/', $path);
		}
		else if(strpos(JPATH_BASE, '\\')>=0) {
			$path = explode("\\", JPATH_BASE);
			$path[count($path)-1] = '';
			$path = implode('\\', $path);
		}

		$url_end = 'images\/'.$this->params->get('documentsPath', $component_params->get('documentsPath', 'documents') ).'\/';
		$path = $path.$url_end;

		/*Checks the file path structure*/
		if(!preg_match('/^'.$path.'/', $media->filepath)) {
			return true;
		}

		/*Checks if the file is a PDF file*/
		if($media->type != "application/pdf") {
			return true;
		}

		/*Retrieves Metadatas from the PDF file*/
		$metas = $this->getMetadata($media->filepath);

		/*The title is contained into PDF metadatas*/
		$title = $metas['title'];
		/*Keywords are contained into PDF metadatas*/
		$keywords = $metas['keywords'];
		/*The description is contained into PDF metadatas*/
		$description = $metas['subject'];
		/*The author is contained into PDF metadatas*/
		$author = $metas['author'];
		/*The alias is obtained from the title*/
		$alias = $this->getAlias($title);
		/*The filename is provided by the media manager component*/
		$filename = $media->filepath;
		/*If the user uses Windows !*/
		$filename = str_replace('\\', '\\\\', $filename);
		/*The MIME type is provided by the media manager component*/
		$mime = $media->type;
		/*The upload date correponds to the current date*/
		$upload_date = JFactory::getDate();
		/*The user who created the document is provided by Joomla*/
		$user = JFactory::getUser();
		$created_by = $user->id;
		/*The created_by_alias is obtained from the current username*/
		$created_by_alias = $user->username;
		/*The creation date is contained into PDF metadatas*/
		$creationDate = $metas['creationDate'];

		/*Retrieves parameters from basic configuration*/
		$acceslevel = $this->params->get('access');
		$catid = $this->params->get('catid');
		$published = $this->params->get('published');

		/*Adds informations concerning the PDF file into the database*/
		$this->addDocument($title, $keywords, $description, $author, $alias, $filename, $mime, $catid, $upload_date, $created_by,
		$created_by_alias, ' ', ' ', '0', ' ', ' ', ' ', ' ', $published, ' ', ' ', $acceslevel, ' ', ' ');

		return true;
	}

	/**
	 * Inserts a document into the database
	 *
	 * @param string $title
	 * @param string $keywords
	 * @param string $description
	 * @param string $author
	 * @param string $alias
	 * @param string $filename
	 * @param string $mime
	 * @param string $catid
	 * @param date $created
	 * @param int $created_by
	 * @param string $created_by_alias
	 * @param date $modified
	 * @param int $modified_by
	 * @param int $hits
	 * @param string $params
	 * @param string $language
	 * @param int $featured
	 * @param int $ordering
	 * @param date $published
	 * @param date $publish_up
	 * @param int $access_level
	 * @param int $checked_out
	 * @param date $checked_out_time
	 */


	public function addDocument($title, $keywords, $description, $author, $alias, $filename, $mime, $catid, $created, $created_by,
	$created_by_alias, $modified, $modified_by, $hits, $params, $language, $featured, $ordering, $published, $publish_up, $publish_down,$access_level,
	$checked_out, $checked_out_time)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/*The Add query*/
		$query = "INSERT INTO #__document(id, title, keywords, description, author, alias, filename, mime, catid,
		created, created_by, created_by_alias, modified, modified_by, hits, params, language, featured, ordering,
		published, publish_up, publish_down, access, checked_out, checked_out_time) VALUES ('', '".$title."', '".$keywords."',
		'".$description."', '".$author."', '".$alias."', '".$filename."', '".$mime."', '".$catid."', '".$created."', '".$created_by."',
		'".$created_by_alias."', '".$modified."','".$modified_by."', '".$hits."', '".$params."', '".$language."', '".$featured."', 
		'".$ordering."', '".$published."', '".$publish_up."','".$publish_down."', '".$access_level."','".$checked_out."', '".$checked_out_time."')";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Returns a unique representation of a string
	 * @param string $string
	 */

	public function getAlias($string)
	{
		/*Alias Structure : hello mr Robinson ==> HELLO_MR_ROBINSON_898 (UPPERCASE + WORD SEPARATOR + RANDOM NUMBER AT THE END)*/
		/*To uppercase*/
		$uppercase = strtoupper($string);
		/*Replaces spaces by an underscore character*/
		$no_space = str_replace(' ','_',$uppercase);
		/*Random number*/
		srand(time());
		$random = (rand()%1000);
		return $no_space."_".$random;
	}

	/**
	 * Returns the metadatas associated to the PDF file
	 * @param string $pdfPath The PDF file path
	 */

	public function getMetadata($pdfPath)
	{
		$pdf = Zend_Pdf::load($pdfPath);

		$metadata = $pdf->getMetadata();

		$metas = array();

		/*If the pdf file is generated with the version 1.6 or higher
		 * of pdf format, metadatas are contained in the associated XMP file (XML/RDF format)*/
		if($metadata!="") {

			$metadataDOM = new DOMDocument();
			$metadataDOM->loadXML($metadata);

			$xpath = new DOMXPath($metadataDOM);
			$pdfPreffixNamespaceURI = $xpath->query('/rdf:RDF/rdf:Description')->item(0)->lookupNamespaceURI('pdf');
			$xpath->registerNamespace('pdf', $pdfPreffixNamespaceURI);

			$titleNode = $xpath->query('/rdf:RDF/rdf:Description/pdf:Title')->item(0);
			$title = $titleNode->nodeValue;

			$authorNode = $xpath->query('/rdf:RDF/rdf:Description/pdf:Author')->item(0);
			$author = $authorNode->nodeValue;

			$subjectNode = $xpath->query('/rdf:RDF/rdf:Description/pdf:Subject')->item(0);
			$subject = $subjectNode->nodeValue;

			$keyWordsNode = $xpath->query('/rdf:RDF/rdf:Description/pdf:Keywords')->item(0);
			$keywords = $keyWordsNode->nodeValue;

			$creationDateNode = $xpath->query('/rdf:RDF/rdf:Description/pdf:CreationDate')->item(0);
			$creationDate = $this->parseMetadataDate($creationDateNode->nodeValue);

			$metas['title'] = $title;
			$metas['subject'] = $subject;
			$metas['author'] = $author;
			$metas['keywords'] = $keywords;
			$metas['creationDate'] = $creationDate;
		}
		/*Else for version 1.5 or lower, metadatas are retrieved from the PDF file directly*/
		else {
			$subject = $this->cleanString($pdf->properties['Subject']);
			$title = $this->cleanString($pdf->properties['Title']);
			$author = $this->cleanString($pdf->properties['Author']);
			$keywords = $this->cleanString($pdf->properties["Keywords"]);
			$date = $this->parseMetadataDate($pdf->properties['CreationDate']);
			$language = $pdf->properties['Language'];

			$metas['title'] = $title;
			$metas['subject'] = $subject;
			$metas['author'] = $author;
			$metas['keywords'] = $keywords;
			$metas['language'] = $language;
			$metas['creationDate'] = $date;
		}

		return $metas;
	}

	/**
	 * Parses the Zend_PHP date into a GMT format
	 * @param string $string the Zend_PHP metadata date
	 */

	public function parseMetadataDate($string)
	{
		/*The original format before parsing : 'D:20101217131030' (Pattern => D:YYYYMMDDHHmmSS)*/
		$year = $string[2].$string[3].$string[4].$string[5];
		$month = $string[6].$string[7];
		$day = $string[8].$string[9];
		$hour = $string[10].$string[11];
		$minutes = $string[12].$string[13];
		return $year."/".$month."/".$day." ".$hour.":".$minutes;
	}


	/**
	 * Remove special characters on the specified string
	 * @param string $string the string to clean
	 */

	public function cleanString($string)
	{
		// Replace other special chars
		$specialCharacters = array('#' => '','$' => '','%' => '','&' => '','@' => '','.' => '','�' => '','+' => '','=' => '','�' => '','\\' => '',
		'/' => '');
		while (list($character, $replacement) = each($specialCharacters)) {
			$string = str_replace($character, '-' . $replacement . '-', $string);
		}
		// Remove all remaining other unknown characters
		$string = preg_replace('/[^a-zA-Z0-9-]/', ' ', $string);
		//Formatting the string to be added into the DB (because metadatas are malformed...)
		$string = $this->removeSimpleSpace($string);

		return $string;
	}

	/**
	 * Arranges the string to be added into the DB
	 * @param string $string
	 */

	public function removeSimpleSpace($string)
	{
		$array = explode('  ', $string);
		if(count($array)==1){
			if(preg_match("/[a-zA-Z0-9-]{2,}[\s][a-zA-Z0-9-]{2,}/", $array[0])){
				return $array[0];
			}
			else{
				$word_array = explode(' ' ,$array[0]);
				return implode('', $word_array);
			}
		}
		$s = '';
		for($i = 0 ; $i < count($array) ; $i++) {
			$word_array = explode(' ' ,$array[$i]);
			$word = implode('', $word_array);
			if($i==0){
				$s = $word;
			}
			else{
				$s = $s . ' ' .$word;
			}
		}
		return $s;
	}

	/**
	 * Function called before a content deletion
	 * @param string $context The component which launch the event 'ContentBeforeDelete'
	 * @param array $media The array that contains informations concerning the element which was deleted
	 */

	public function onContentBeforeDelete($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return true;
	}

	/**
	 * Function called before a content save
	 * @param string $context The component which launch the event 'ContentBeforeSave'
	 * @param array $media The array that contains informations concerning the element which was saved
	 */

	public function onContentBeforeSave($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return true;
	}

	/**
	 * Function called after a content display
	 * @param string $context The component which launch the event 'ContentAfterDisplay'
	 * @param array $media The array that contains informations concerning the element which was displayed
	 */

	public function onContentAfterDisplay($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return "";
	}

	/**
	 * Function called before a content display
	 * @param string $context The component which launch the event 'ContentBeforeDisplay'
	 * @param array $media The array that contains informations concerning the element which was displayed
	 */

	public function onContentBeforeDisplay($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return "";
	}
}