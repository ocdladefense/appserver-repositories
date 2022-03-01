<?php

class ProductsModule extends Module {


    // This query ignores tickets.
    private $productsOnly = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c = null AND ClickpdxCatalog__IsOption__c = False";

    // This query targets event tickets.
    private $tickets = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c != null AND ClickpdxCatalog__IsOption__c = False";

    // Name, Agenda__c, Banner_Location_Text__c

    public function __construct() {

        parent::__construct();

    }

    public function doXML($index = "ocdla_products") {
        $api = $this->loadForceApi();

        $result = $api->query($this->productsOnly);

        $records = $result->getRecords();

        //var_dump($result);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $docset = $dom->createElementNS('http://www.example.com/Docset','sphinx:docset');

        $schema = $dom->createElement('sphinx:schema');
        

        $field_1 = $dom->createElement('sphinx:field');
        $field_1->setAttribute('name', 'subject');

        $field_2 = $dom->createElement('sphinx:field');
        $field_2->setAttribute('name', 'content');

        $field_3 = $dom->createElement('sphinx:field');
        $field_3->setAttribute('name', 'product_name');

        $attr_1 = $dom->createElement('sphinx:attr');
        $attr_1->setAttribute('name', 'product_id');
        $attr_1->setAttribute('type', 'string');

        $attr_2 = $dom->createElement('sphinx:attr');
        $attr_2->setAttribute('name', 'published');
        $attr_2->setAttribute('type', 'timestamp');

        $atindex = $dom->createElement('sphinx:attr');
        $atindex->setAttribute('name', 'indexName');
        $atindex->setAttribute('type', 'string');

        $attr_3 = $dom->createElement('sphinx:attr');
        $attr_3->setAttribute('name', 'author_id');
        $attr_3->setAttribute('type', 'int');
        $attr_3->setAttribute('bits', '16');
        $attr_3->setAttribute('default', '1');


        $schema->appendChild($field_1);
        $schema->appendChild($field_2);
        $schema->appendChild($field_3);
        $schema->appendChild($atindex);
        $schema->appendChild($attr_1);
        $schema->appendChild($attr_2);
        $schema->appendChild($attr_3);

        $docset->appendChild($schema);
        


        $count = 1;


        foreach($records as $product)
        {
            $doc = $dom->createElement('sphinx:document');

            $doc->setAttribute('id', $count);

            $productId = $dom->createElement('product_id', $product["Id"]);
            $indexName = $dom->createElement('indexName', "ocdla_products");

            $title = $dom->createElement('product_name');
            $title->appendChild($dom->createCDATASection($product["Name"]));
   
            $content = $dom->createElement('content');
            $standard = $product["Description"];
            $html = $product["ClickpdxCatalog__HtmlDescription__c"];
            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);

            $description = utf8_encode($this->getDescription($html, $standard));
            
            $description = str_replace("\n","",$description);
            
            
            $content->appendChild($dom->createCDATASection($description));

            $doc->appendChild($productId);
            $doc->appendChild($indexName);
            $doc->appendChild($title);
            $doc->appendChild($content);

            $docset->appendChild($doc);
            $count++;
        }

        //var_dump($dom);
        
        //var_dump($result->getRecords());

        $dom->appendChild($docset);

        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;


        // print $dom->saveXML();exit;
        return $dom->saveXML();
    }



    private function getDescription($html, $text, $default = "") {


       if(!empty($html)) return $html;
       return !empty($text) ? $test : $default;
    }
}