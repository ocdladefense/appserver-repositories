<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class MemberRepository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, (SELECT Interest__c FROM AreasOfInterest__r) FROM Contact WHERE Ocdla_Current_Member_Flag__c = True";


    private $repository = "ocdla_members";



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

        //var_dump($member["AreasOfInterest__r"] );
        //exit;

        //phpinfo();
        //exit;

        var_dump($member);

        $title = $member["Name"];
        if($member["AreasOfInterest__r"] != null)
        {
            //$description = implode(", ", $member["AreasOfInterest__r"]);
            $expertise = $member["AreasOfInterest__r"];
            foreach($expertise as $expert)
            {
                var_dump($expert);
            }
            exit;
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