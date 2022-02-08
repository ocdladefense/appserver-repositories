<?php

class ProductsModule extends Module {

    public function __construct() {

        parent::__construct();

    }

    public function doXML() {
        $tpl = new Template("placeholder");
        $tpl->addPath(__DIR__ . "/templates");

        $api = $this->loadForceApi();

        $result = $api->query("SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = true AND ClickpdxCatalog__IsOption__c = false");
        
        var_dump($result->getRecords());
        $html = $tpl;

        return $tpl;
    }
}