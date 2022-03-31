<?php

class ProductsModule extends Module {


    private $indexes = array(
        "product2" => "Product2Repository"
    );


    // This query targets event tickets.
    private $tickets = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c != null AND ClickpdxCatalog__IsOption__c = False";

    private $events = "SELECT Id, Name, Agenda__c, Banner_Location_Text__c FROM Event__c";

    private $members = "SELECT Id, Name FROM Contact WHERE Ocdla_Current_Member_Flag__c = True";
    




    public function __construct() {

        parent::__construct();
    }



    public function doXML($source = "Product2") {


        $meta = $this->indexes[$source];
        // var_dump($meta);exit;
        $class = new $meta;

        $api = $this->loadForceApi();

        $result = $api->query($class->getQuery());

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
        $field_3->setAttribute('name', 'recordName');

        $attr_1 = $dom->createElement('sphinx:attr');
        $attr_1->setAttribute('name', 'alt_id');
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


        foreach($records as $record)
        {
            $doc = $dom->createElement('sphinx:document');

            $doc->setAttribute('id', $count);

            $altId = $dom->createElement('alt_id', $record["Id"]);
           

            // Delegate processing of name and content
            // to a custom class.
            list($title,$description) = $class->getNode($record);

            $indexName = $dom->createElement('indexName', $class->getRepository());
            $name = $dom->createElement('recordName');
            $name->appendChild($dom->createCDATASection($title));


            $content = $dom->createElement('content');
            $content->appendChild($dom->createCDATASection($description));

            $doc->appendChild($altId);
            $doc->appendChild($indexName);
            $doc->appendChild($name);
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




}