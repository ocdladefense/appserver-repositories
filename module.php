<?php

class RepositoryModule extends Module {


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
            ),
            "expert" => array(
                "handler"   => "ExpertRepository",
                "start"     => 40000,
            ),
            "event" => array(
                "handler"   => "EventRepository",
                "start"     => 50000,
            ),
            "video" => array(
                "handler"   => "VideoRepository",
                "start"     => 60000,
            )
        );

        return $indexes[$key];
    }


    // This query targets event tickets.
    /*
    private $tickets = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c != null AND ClickpdxCatalog__IsOption__c = False";

    private $events = "SELECT Id, Name, Agenda__c, Banner_Location_Text__c FROM Event__c";

    private $members = "SELECT Id, Name FROM Contact WHERE Ocdla_Current_Member_Flag__c = True";
    */




    public function __construct() {

        parent::__construct();
    }



    public function doXml($source) {


        $api = loadapi();

        $meta = $this->getMeta($source);
        // var_dump($meta);exit;
        $classname      = $meta["handler"];
        $startIndex     = isset($meta["start"]) ? $meta["start"] : 1;
        $repo           = new $classname();

        

        $result = $api->query($repo->getQuery());

        $records = $result->getRecords();


        //var_dump($result);
        $docset = $repo->createElementNS("http://www.example.com/Docset","sphinx:docset");

        $schema = $repo->getSchema();


        $docset->appendChild($schema);

       
        $nodes = $repo->getDocumentNodes($records,$startIndex);
        // var_dump($nodes);
        //var_dump($result->getRecords());
        foreach($nodes as $doc) {
            $docset->appendChild($doc);
        }

        $repo->appendChild($docset);

        $this->formatOutput = true;
        $this->preserveWhiteSpace = false;

        
        return $repo->saveXML();
    }




}