<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */

class Product2Repository {


    // This query ignores tickets.
    private $query = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c = NULL AND ClickpdxCatalog__IsOption__c = False";


    private $repository = "ocdla_products";



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
    public function getNode($product) {
        $title = $product["Name"];
        $standard = $product["Description"] ?? " ";
        $html = $product["ClickpdxCatalog__HtmlDescription__c"] ?? " ";
        $html = utf8_decode($html);
        $html = preg_replace('/\x{00A0}+/mis', " ", $html);

        $description = $standard . $html;

        //$description = utf8_encode($this->getDescription($html, $standard));

        $description =  utf8_encode($description);
        
        $description = str_replace("\n","",$description);

        return array($title,$description);
    }


    private function getDescription($html, $text, $default = "") {


        if(!empty($html)) return $html;
        return !empty($text) ? $text : $default;
     }


}