<?php

namespace Ximdex\Utils;

use SimpleXMLElement;

class SimpleXMLExtended extends SimpleXMLElement
{
    /**
     * @param string $cdata_text
     */
    public function addCData(string $cdata_text): void
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

    /**
     * Create a child with CDATA value
     *
     * @param string $name The name of the child element to add
     * @param string $cdata_text The CDATA value of the child element
     */
    public function addChildCData(string $name, string $cdata_text): void
    {
        $child = $this->addChild($name);
        $child->addCData($cdata_text);
    }
}