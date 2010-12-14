<?php


// No direct access
defined('_JEXEC') or die;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILENAME__));

require_once 'Zend/Pdf.php';

jimport('joomla.plugin.plugin');

/**
 * PDF Document Content Plugin
 */

class plgContentDocumentPDF extends JPlugin
{

	public function onContentAfterDelete($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query = "DELETE FROM  #__document WHERE filename = '$media->filepath'";

		$db->setQuery( $query );
		$db->query();
		return true;
	}

	public function onContentAfterSave($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}

		/*VERIFYING THAT THE FILE IS A PDF*/
		if($media->type != "application/pdf") {
			JError::raiseNotice(100, "DOCUMENT PDF PLUGIN :: THIS DOCUMENT IS NOT A PDF !");
			return true;
		}

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
		// if the user use window
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

		$this->addDocument($title, $keywords, $description, $author, $alias, $filename, $mime, ' ', $upload_date, $created_by,
		$created_by_alias, ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

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
		$uppercase = strtoupper($string);
		$no_space = str_replace(' ','_',$uppercase);
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
		 * of pdf format, metadats are contained in the associated XMP file (XML/RDF format)*/
		if($metadata!="") {
			echo "METAS : ".$metadata;

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
			$title = $this->cleanString($pdf->properties['Title']);
			$author = $this->cleanString($pdf->properties['Author']);
			$subject = $this->cleanString($pdf->properties['Subject']);
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
		$string = preg_replace('/^[-]+/', '', $string);
		$string = preg_replace('/[-]+$/', '', $string);
		$string = preg_replace('/[-]{2,}/', ' ', $string);
	

		$string = $this->removeSimpleSpace($string);
		
		
		return $string;
	}

	public function removeSimpleSpace($string)
	{	
		for($i = 0 ; $i< count($string) -1; $i++)
		{
			if($string[$i] == ' ')
			{
				$string[$i] = '';
				$i++;
			}
		}
		return $string;
	}
	
	
	public function onContentBeforeDelete($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		JError::raiseNotice( 100, "CONTENT_BEFORE_DELETE");
		return true;
	}

	public function onContentBeforeSave($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		JError::raiseNotice( 100, "CONTENT_BEFORE_SAVE");
		return true;
	}

	public function onContentAfterDisplay($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return "";
	}

	public function onContentBeforeDisplay($context, &$media)
	{
		$app = JFactory::getApplication();

		if($context!="com_media.file" && $context!="com_document.file"){
			return true;
		}
		return "";
	}
}