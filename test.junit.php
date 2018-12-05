<?php  // XML-Quellen laden
$xml = new \DOMDocument;
$xml->load('test.junit.xml');

$xsl = new \DOMDocument;
$xsl->load('test.stylesheet.xml');

// Prozessor instanziieren und konfigurieren
$proc = new \XSLTProcessor;
$proc->importStyleSheet($xsl); // XSL Document importieren

echo $proc->transformToXML($xml);