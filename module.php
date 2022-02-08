<?php

class ProductsModule extends Module {

    public function __construct() {

        parent::__construct();

    }

    public function doXML() {
        $tpl = new Template("placeholder");
        $tpl->addPath(__DIR__ . "/templates");

        $html = $tpl;

        return $tpl;
    }
}