<?php
/**
 * @version		$Id$
 * @package		Documents
 * @subpackage	Plugins
 * @copyright	Copyright (C) 2010 - today Master ICONE, University of La Rochelle, France.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

//    cd /home/walien/eclipse/workspaces/workspace_u/com_document/plg_content_documentpdf

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
		JError::raiseNotice( 100, "CONTENT_AFTER_DELETE");
		return true;
	}

	public function onContentAfterSave($context, &$media)
	{
		$app = JFactory::getApplication();

		/*VERIFYING THAT THE FILE IS A PDF*/
		if($media->type != "application/pdf") {
			JError::raiseNotice(100, "DOCUMENT PDF PLUGIN :: THIS DOCUMENT IS NOT A PDF !");
			return true;
		}

		$metas = $this->getMetadata($media->filepath);

		JError::raiseNotice(100, "TITLE : ".$metas['title']);
		JError::raiseNotice(100, "SUBJECT : ". $metas['subject']);
		JError::raiseNotice(100, "AUTHOR : ". $metas['author']);
		JError::raiseNotice(100, "KEYWORDS : ". $metas['keywords']);
		JError::raiseNotice(100, "CREATION DATE : ". $metas['creationDate']);

		return true;
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
		$string = preg_replace('[ ]', '', $string);

		return $string;
	}


	public function onContentBeforeDelete($context, &$media)
	{
		$app = JFactory::getApplication();
		JError::raiseNotice( 100, "CONTENT_BEFORE_DELETE");
		return true;
	}

	public function onContentBeforeSave($context, &$media)
	{
		$app = JFactory::getApplication();
		JError::raiseNotice( 100, "CONTENT_BEFORE_SAVE");
		return true;
	}

	public function onContentAfterDisplay($context, &$media)
	{
		$app = JFactory::getApplication();
		return "";
	}

	public function onContentBeforeDisplay($context, &$media)
	{
		$app = JFactory::getApplication();
		return "";
	}
}
