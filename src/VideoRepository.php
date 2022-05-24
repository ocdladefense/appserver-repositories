<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class VideoRepository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, ResourceId__c, Event__c, Event__r.Name FROM Media__c WHERE Published__c = True";


    private $repository = "ocdla_videos";



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
    public function getNode($media) {
        // var_dump($media);exit;
        $title = $media["Name"];
        $event = $media["Event__r"]["Name"];

        $description = $title ." ". $event;

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