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



    public function getSchema() {

        $schema = $this->createElement('sphinx:schema');
          

        $field_1 = $this->createElement('sphinx:field');
        $field_1->setAttribute('name', 'subject');

        $field_2 = $this->createElement('sphinx:field');
        $field_2->setAttribute('name', 'content');

        // $field_3 = $dom->createElement('sphinx:field');
        // $field_3->setAttribute('name', 'name');

        $attr_1 = $this->createElement('sphinx:attr');
        $attr_1->setAttribute('name', 'alt_id');
        $attr_1->setAttribute('type', 'string');

        // http://sphinxsearch.com/docs/manual-2.3.2.html#conf-xmlpipe-attr-string
        $title = $this->createElement('sphinx:attr');
        $title->setAttribute('name', 'title');
        $title->setAttribute('type', 'string');

        // $attr_2 = $dom->createElement('sphinx:attr');
        // $attr_2->setAttribute('name', 'published');
        // $attr_2->setAttribute('type', 'timestamp');

        $atindex = $this->createElement('sphinx:attr');
        $atindex->setAttribute('name', 'indexname');
        $atindex->setAttribute('type', 'string');

        $attr_3 = $this->createElement('sphinx:attr');
        $attr_3->setAttribute('name', 'author_id');
        $attr_3->setAttribute('type', 'int');
        $attr_3->setAttribute('bits', '16');
        $attr_3->setAttribute('default', '1');


        $schema->appendChild($field_1);
        $schema->appendChild($field_2);
        // $schema->appendChild($field_3);
        $schema->appendChild($atindex);
        $schema->appendChild($attr_1);
        // $schema->appendChild($attr_2);
        $schema->appendChild($attr_3);
        $schema->appendChild($title);
        
        return $schema;
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

            $attr_title = $this->createElement('title');
            $attr_title->appendChild($this->createCDATASection($title));

            $content = $this->createElement('content');
            $content->appendChild($this->createCDATASection($description));

            $altId = $this->createElement('alt_id', $record["Id"]);
            $indexName = $this->createElement('indexname', $this->getRepository());

            $doc->appendChild($subject);
            $doc->appendChild($content);
            $doc->appendChild($altId);
            $doc->appendChild($indexName);
            $doc->appendChild($attr_title);
            

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