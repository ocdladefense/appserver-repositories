<?php

class ProductsModule extends Module {


    // This query ignores tickets.
    private $productsOnly = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c = null AND ClickpdxCatalog__IsOption__c = False";

    // This query targets event tickets.
    private $tickets = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c, IsActive FROM Product2 WHERE IsActive = True AND Event__c != null AND ClickpdxCatalog__IsOption__c = False";



    public function __construct() {

        parent::__construct();

    }

    public function doXML() {
        $api = $this->loadForceApi();

        $result = $api->query($this->productsOnly);

        $records = $result->getRecords();

        //var_dump($result);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $docset = $dom->createElementNS('http://www.example.com/Docset','sphinx:docset');

        $schema = $dom->createElement('sphinx:schema');
        

        $field_1 = $dom->createElement('sphinx:field');
        $field_1->setAttribute('name', 'subject');

        $field_2 = $dom->createElement('sphinx:field');
        $field_2->setAttribute('name', 'content');

        $field_3 = $dom->createElement('sphinx:field');
        $field_3->setAttribute('name', 'product_name');

        $attr_1 = $dom->createElement('sphinx:attr');
        $attr_1->setAttribute('name', 'product_id');
        $attr_1->setAttribute('type', 'string');

        $attr_2 = $dom->createElement('sphinx:attr');
        $attr_2->setAttribute('name', 'published');
        $attr_2->setAttribute('type', 'timestamp');

        $attr_3 = $dom->createElement('sphinx:attr');
        $attr_3->setAttribute('name', 'author_id');
        $attr_3->setAttribute('type', 'int');
        $attr_3->setAttribute('bits', '16');
        $attr_3->setAttribute('default', '1');


        $schema->appendChild($field_1);
        $schema->appendChild($field_2);
        $schema->appendChild($field_3);
        $schema->appendChild($attr_1);
        $schema->appendChild($attr_2);
        $schema->appendChild($attr_3);

        $docset->appendChild($schema);
        


        $count = 1;


        foreach($records as $product)
        {
            $doc = $dom->createElement('sphinx:document');

            $doc->setAttribute('id', $count);

            $productId = $dom->createElement('product_id', $product["Id"]);

            $title = $dom->createElement('product_name');
            $title->appendChild($dom->createCDATASection($product["Name"]));
   
            $content = $dom->createElement('content');
            $standard = $product["Description"];
            $html = $product["ClickpdxCatalog__HtmlDescription__c"];
            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);

            $description = utf8_encode($this->getDescription($html, $standard));
            
            $description = str_replace("\n","",$description);
            
            


            // print '<![CDATA[ <p><span style="color: #111111;"><span style="font-family: verdana;"><span style="font-size: 11.0pt;"><img alt="User-added image" src="https://ocdla--ocdpartial--c.documentforce.com/servlet/rtaImage?eid=01t0a0000045g3H&amp;feoid=00N0a00000COQDS&amp;refid=0EM0a0000000obg" style="height: 142px; width: 500px;"></img></span></span></span><br><br><b><span style="font-family: verdana; font-size: 12.0pt;">Program</span><br>April 20–21, 2018 • Agate Beach Inn, Newport, OR</b></p> <p class="presentation" style="margin-left: 0in;"><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;"><b>Taking the Kid Gloves Off: An Interactive Examination of Spotting and Interrupting Racism in Juvenile Law</b> </span></span></span><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Kasia Rutledge, Attorney, Portland; Rakeem Washington, Instructor, Portland State University and Portland Community College, Portland<br>(1.5 access to justice credits)</span></span><br>        <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">How Domestic Relations Can Aid Your Dependency Defense Strategy</span></span></span></b><br><i><span style="font-weight: normal;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Annette Smith, Staff Attorney, Public Defender Services of Lane County, Inc., Eugene</span></span></span></i><br>        <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Oh, Why Argue? Let’s Talk Instead: A Not So Intimate Conversation<br>with Joe O’Leary</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Joe O’Leary, Director, OYA, Salem;<br>Emily Simon, Attorney, Portland</span></span><br>        <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Washington State’s “</span></span></span><i><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Roper v. Simmons</span></span></span></i><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">” Moment Has Arrived: Extending the Momentum to Oregon’s Measure 11 Cases</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Nicole McGrath, Attorney, Seattle</span></span><br>        <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Trauma-Informed Lawyers: Best Practices for Self, Client, and System</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Kyra Hazilla, Attorney and LCSW, Portland</span></span><br>        <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">The Great Oracle Visits Juvenile Court </span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Emily Simon, Attorney, Portland; Kevin Ellis, Attorney, Saint Helens<br>(1.25 ethics credits)</span></span><br> <br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Dependency &amp; Delinquency Appellate Update</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Hon. Megan Jacquot, Coos County Circuit Court;<br>Sarah Peterson, Deputy Public Defender, OPDS, Salem</span></span><br>        <br><b><i><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">DHS v. Fabbrini</span></span></span></i><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">: A Case Study</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Jamie Gerlitz, Attorney, Bend</span></span><br>        <br><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;"><b>Legislative Update </b>      </span></span></span><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Representative Chris Gorsek, Oregon House District 49, Salem</span></span><br><br><b><span style="color: #660066;"><span style="font-family: verdana;"><span style="font-size: 12.0pt;">OPDS’s Juvenile Appellate Section: A 10-Year Retrospective</span></span></span></b><br><span style="font-family: verdana;"><span style="font-size: 12.0pt;">Valerie Colas, Chief Defender, OPDS, Salem; Valerie Colas, Deputy Public Defender, OPDS, Salem</span></span><br> <br> </p> ]]>';
            // print $description; exit;
            $content->appendChild($dom->createCDATASection($description));

            $doc->appendChild($productId);
            $doc->appendChild($title);
            $doc->appendChild($content);

            $docset->appendChild($doc);
            $count++;
        }

        //var_dump($dom);
        
        //var_dump($result->getRecords());

        $dom->appendChild($docset);

        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;



        return $dom->saveXML();
    }



    private function getDescription($html, $text, $default = "") {


       if(!empty($html)) return $html;
       return !empty($text) ? $test : $default;
    }
}