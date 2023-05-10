<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class ExpertRepository extends Repository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, (SELECT Interest__c FROM AreasOfInterest__r) FROM Contact WHERE Ocdla_Is_Expert_Witness__c = true";


    private $repository = "ocdla_experts";



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
    public function getNode($expert) {

        $title = $expert["Name"];
        if($expert["AreasOfInterest__r"] != null)
        {
            $expertise = $expert["AreasOfInterest__r"];
            $records = $expertise["records"];
            foreach($records as $record)
            {
                $description .= $record["Interest__c"] . " ";
            }
            
        }
        else
        {
            $description = " ";
        }

        return array($title,$description);
    }


    
    private function decode($encoded) {
        $html = utf8_decode($encoded);
        $html = preg_replace('/\x{00A0}+/mis', " ", $html);

        $description = utf8_encode($html);
        
        return str_replace("\n","",$description);
    }



}