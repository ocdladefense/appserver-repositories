<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class EventRepository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name FROM Event__c";


    private $repository = "ocdla_events";



    public function getQuery() {
        return $this->query;
    }

    public function getRepository() {
        return $this->repository;
    }



    /**
     * @method getNode
     * 
     * Return a title and description for populating an indexed document.
     */
    public function getNode($member) {

        $title = $member["Name"];
        $description = "";

        return array($title,$description);
    }


    
    private function decode($encoded) {
        $html = utf8_decode($encoded);
        $html = preg_replace('/\x{00A0}+/mis', " ", $html);

        $description = utf8_encode($html);
        
        return str_replace("\n","",$description);
    }



}