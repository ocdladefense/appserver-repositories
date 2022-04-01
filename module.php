<?php

class ProductsModule extends Module {


    private function getMeta($key) {
        $indexes = array(
            "product" => array(
                "handler"   => "Product2Repository",
                "start"     => 30000,
            ),
            "member" => array(
                "handler"   => "MemberRepository",
                "start"     => 20000,
            ),
            "car"   => array(
                "handler"   => null,
                "start"     => function($id){ return $id+10000; }
            ),
            "wiki"  => array(
                "handler"   => null,
                "start"     => function($id){ return $id; }
            )
        );

        return $indexes[$key];
    }


    // This query targets event tickets.
    private $tickets = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c != null AND ClickpdxCatalog__IsOption__c = False";

    private $events = "SELECT Id, Name, Agenda__c, Banner_Location_Text__c FROM Event__c";

    private $members = "SELECT Id, Name FROM Contact WHERE Ocdla_Current_Member_Flag__c = True";
    




    public function __construct() {

        parent::__construct();
    }



    public function doXML($source = "product") {


        $meta = $this->getMeta($source);
        // var_dump($meta);exit;
        $classname  = $meta["handler"];
        $start      = isset($meta["start"]) ? $meta["start"] : 1;
        $class      = new $classname;

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

        // $field_3 = $dom->createElement('sphinx:field');
        // $field_3->setAttribute('name', 'name');

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
        // $schema->appendChild($field_3);
        $schema->appendChild($atindex);
        $schema->appendChild($attr_1);
        $schema->appendChild($attr_2);
        $schema->appendChild($attr_3);

        $docset->appendChild($schema);


        foreach($records as $record)
        {
            $doc = $dom->createElement('sphinx:document');

            $doc->setAttribute('id', $start);

            
           

            // Delegate processing of name and content
            // to a custom class.
            list($title,$description) = $class->getNode($record);
            if(empty($title)) continue;

            
            $subject = $dom->createElement('subject');
            $subject->appendChild($dom->createCDATASection($title));

            $content = $dom->createElement('content');
            $content->appendChild($dom->createCDATASection($description));

            $altId = $dom->createElement('alt_id', $record["Id"]);
            $indexName = $dom->createElement('indexName', $class->getRepository());

            $doc->appendChild($subject);
            $doc->appendChild($content);
            $doc->appendChild($altId);
            $doc->appendChild($indexName);

            $docset->appendChild($doc);
            $start++;
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