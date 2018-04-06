<?php
namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\NodeTypes\HTMLDocumentNode;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Models\Channel;

class ViewPrepareHTML extends AbstractView implements IView
{
    const MACRO_CODE = "/@@@RMximdex\.code\((.*),(.*)\)@@@/";
    
    private $nodeID;

    /**
     * {@inheritdoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        if (! isset($args['NODEID']) || empty($args['NODEID'])) {
            Logger::error('Argument nodeId not found in ViewPrepareHTML');
            return false;
        }
        $this->nodeID = $args['NODEID'];
        
        // Get the content
        $content = $this->retrieveContent($pointer);
        $document = ($content !== false) ? HTMLDocumentNode::renderHTMLDocument($args['NODEID'], $content) : false;
        
        // Process macros
        if ($document !== false) {
            $document = preg_replace_callback(self::MACRO_CODE, array(
                $this,
                'getCodeTranslation'
            ), $document);
        }
        
        // Return the pointer to the transformed content
        return $this->storeTmpContent($document);
    }

    /**
     * @param $matches
     * @return string
     */
    private function getCodeTranslation($matches)
    {
        // Get channel
        $properties = InheritedPropertiesManager::getValues($this->nodeID, true);
        if (!isset($properties['Channel']) or !$properties['Channel']) {
            Logger::error('There is not a channel defined for the document with ID: ' . $this->nodeID);
            return '';
        }
        $channelProp = current($properties['Channel']);
        $channelID = $channelProp['Id'];
        $channel = new Channel($channelID);

        //TODO ajlucena!
        
        // Get function
        $function = $matches[1];
        
        /*
        $translations['php']['include'] = 'include(%s)';
        $translations['python']['include'] = 'import({{path}})';
        $translation = isset($translations[$channel][$function]) ? $translations[$channel][$function] : '';
        return sprintf($translation, $matches[2]);
        */
        
        return '';
    }
}