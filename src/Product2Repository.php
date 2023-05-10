<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class Product2Repository extends Repository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c = NULL AND ClickpdxCatalog__IsOption__c = False";


    private $repository = "ocdla_products";



    public function getQuery() {
        return $this->query;
    }

    public function getRepository() {
        return $this->repository;
    }

    public function __construct() {
        parent::__construct();
    }







    public function getDocumentNodes($records,$start) {


        $nodes = [];

        foreach($records as $record)
        {
            $doc = $this->createElement('sphinx:document');

            $doc->setAttribute('id', $start);

            // Delegate processing of name and content
            // to a custom class.
            list($title,$description) = $this->getNode($record);
            if(empty($title)) continue;

            
            $subject = $this->createElement('subject');
            $subject->appendChild($this->createCDATASection($title));

            $content = $this->createElement('content');
            $content->appendChild($this->createCDATASection($description));

            $altId = $this->createElement('alt_id', $record["Id"]);
            $indexName = $this->createElement('indexname', $this->getRepository());

            $doc->appendChild($subject);
            $doc->appendChild($content);
            $doc->appendChild($altId);
            $doc->appendChild($indexName);

            $nodes []= $doc;
            $start++;
        }

        return $nodes;
    }   
    
    

    
    /**
     * @method getNode
     * 
     * Return a title and description for populating an indexed document.
     */
    public function getNode($product) {
        $title = $product["Name"];
        $standard = $product["Description"] ?? " ";
        $html = $product["ClickpdxCatalog__HtmlDescription__c"] ?? " ";
        $html = utf8_decode($html);
        $html = preg_replace('/\x{00A0}+/mis', " ", $html);

        $description = $standard . $html;

        //$description = utf8_encode($this->getDescription($html, $standard));

        $description =  utf8_encode($description);
        
        $description = str_replace("\n","",$description);

        $description = strip_tags($description, "<br>");

        $description = str_replace("<br>", " ",$description);

        return array($title,$description);
    }


    private function getDescription($html, $text, $default = "") {


        if(!empty($html)) return $html;
        return !empty($text) ? $text : $default;
     }


}