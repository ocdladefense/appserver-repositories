<?php
/**
 * Get a single node (a.k.a, sphinx document) for our XML document.
 */
abstract class Repository extends DOMDocument {

    protected $root;


    protected function __construct() {

        // $this->dom = new DOMDocument('1.0', 'UTF-8');
        parent::__construct("1.0","UTF-8");
    }



    public function getRootElement() {
        return $this->root;
    }



    public function getSchema() {

        $schema = $this->createElement('sphinx:schema');
          

        $field_1 = $this->createElement('sphinx:field');
        $field_1->setAttribute('name', 'subject');

        $field_2 = $this->createElement('sphinx:field');
        $field_2->setAttribute('name', 'content');

        // $field_3 = $dom->createElement('sphinx:field');
        // $field_3->setAttribute('name', 'name');

        $attr_1 = $this->createElement('sphinx:attr');
        $attr_1->setAttribute('name', 'alt_id');
        $attr_1->setAttribute('type', 'string');

        // $attr_2 = $dom->createElement('sphinx:attr');
        // $attr_2->setAttribute('name', 'published');
        // $attr_2->setAttribute('type', 'timestamp');

        $atindex = $this->createElement('sphinx:attr');
        $atindex->setAttribute('name', 'indexname');
        $atindex->setAttribute('type', 'string');

        $attr_3 = $this->createElement('sphinx:attr');
        $attr_3->setAttribute('name', 'author_id');
        $attr_3->setAttribute('type', 'int');
        $attr_3->setAttribute('bits', '16');
        $attr_3->setAttribute('default', '1');


        $schema->appendChild($field_1);
        $schema->appendChild($field_2);
        // $schema->appendChild($field_3);
        $schema->appendChild($atindex);
        $schema->appendChild($attr_1);
        // $schema->appendChild($attr_2);
        $schema->appendChild($attr_3);

        return $schema;
    }

    public function getDocumentNodes($records,$start) {


        $nodes = [];

        foreach($records as $record)
        {
            $doc = $this->createElement('sphinx:document');

            $doc->setAttribute('id', $start);

            // Delegate processing of name and content
            // to a custom class.
            list($title,$description) = $this->getNode($record);
            if(empty($title)) continue;

            
            $subject = $this->createElement('subject');
            $subject->appendChild($this->createCDATASection($title));

            $content = $this->createElement('content');
            $content->appendChild($this->createCDATASection($description));

            $altId = $this->createElement('alt_id', $record["Id"]);
            $indexName = $this->createElement('indexname', $this->getRepository());

            $doc->appendChild($subject);
            $doc->appendChild($content);
            $doc->appendChild($altId);
            $doc->appendChild($indexName);

            $nodes []= $doc;
            $start++;
        }

        return $nodes;
    }   
}