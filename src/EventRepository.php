<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class EventRepository extends Repository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, Agenda__c, Overview__c, Banner_Location_Text__c, Venue__c FROM Event__c";


    private $repository = "ocdla_events";



    public function getQuery() {
        return $this->query;
    }

    public function getRepository() {
        return $this->repository;
    }


    public function __construct() {
        parent::__construct();
    }

    /**
     * @method getNode
     * 
     * Return a title and description for populating an indexed document.
     */
    public function getNode($event) {

        $title = $event["Name"];
        $agenda = $event["Agenda__c"] ?? " ";
        $overview = $event["Overview__c"] ?? " ";
        $banner = $event["Banner_Location_Text__c"] ?? " ";
        $venue = $event["Venue__c"] ?? " ";
        $description = $agenda . $overview . $banner . $venue;

        $description = strip_tags($description, "<br>");

        $description = str_replace("<br>", " ",$description);


        return array($title,$description);
    }


    
    private function decode($encoded) {
        $html = utf8_decode($encoded);
        $html = preg_replace('/\x{00A0}+/mis', " ", $html);

        $description = utf8_encode($html);
        
        return str_replace("\n","",$description);
    }



}