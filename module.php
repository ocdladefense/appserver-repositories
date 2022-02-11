<?php

class ProductsModule extends Module {

    public function __construct() {

        parent::__construct();

    }

    public function doXML() {
        $api = $this->loadForceApi();

        $result = $api->query("SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = true AND ClickpdxCatalog__IsOption__c = false");

        $result = $result->getRecords();

        //var_dump($result);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->appendChild($dom->createElementNS('http://www.example.com/Docset','sphinx:docset'));
        $schema = $element->appendChild($dom->createElement('sphinx:schema'));
        $fieldOne = $schema->appendChild($dom->createElement('sphinx:field'));
        $fieldOne->setAttribute('name', 'subject');
        $fieldTwo = $schema->appendChild($dom->createElement('sphinx:field'));
        $fieldTwo->setAttribute('name', 'content');
        $fieldThree = $schema->appendChild($dom->createElement('sphinx:field'));
        $fieldThree->setAttribute('name', 'product_name');
        $attrOne = $schema->appendChild($dom->createElement('sphinx:attr'));
        $attrOne->setAttribute('name', 'product_id');
        $attrOne->setAttribute('type', 'string');
        $attrTwo = $schema->appendChild($dom->createElement('sphinx:attr'));
        $attrTwo->setAttribute('name', 'published');
        $attrTwo->setAttribute('type', 'timestamp');
        $attrThree = $schema->appendChild($dom->createElement('sphinx:attr'));
        $attrThree->setAttribute('name', 'author_id');
        $attrThree->setAttribute('type', 'int');
        $attrThree->setAttribute('bits', '16');
        $attrThree->setAttribute('default', '1');

        $count = 1;


        foreach($result as $products)
        {
            $body = $element->appendChild($dom->createElement('sphinx:document'));
            $body->setAttribute('id', $count++);
            $body->appendChild($dom->createElement('product_id', $products["Id"]));
            $title = $body->appendChild($dom->createElement('product_name'));
            $title->appendChild($dom->createCDATASection($products["Name"]));
            
            if($products["Description"] != null)
            {
                $item = $body->appendChild($dom->createElement('content'));
                $item->appendChild($dom->createCDATASection($products["Description"]));
            }
            elseif($products["ClickpdxCatalog__HtmlDescription__c"] != null)
            {
                $item = $body->appendChild($dom->createElement('content'));
                $item->appendChild($dom->createCDATASection($products["ClickpdxCatalog__HtmlDescription__c"]));
            }
            else
            {
                $item = $body->appendChild($dom->createElement('content'));
                $item->appendChild($dom->createCDATASection(' '));
            }
            
            
        }

        //var_dump($dom);
        
        //var_dump($result->getRecords());

        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;



        return $dom->saveXML();
    }
}