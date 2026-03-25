<?php

class EL_Converter {

    private $mapper;

    public function __construct() {
        $this->mapper = new EL_Mapper();
    }

    public function convert($nodes) {
        $elements = [];

        foreach ($nodes as $node) {
            $elements[] = $this->node($node);
        }

        return $elements;
    }

    private function node($node) {

        $mapped = $this->mapper->get($node['type']);

        $element = [
            'id' => substr(md5(uniqid()), 0, 8),
            'settings' => [],
            'elements' => []
        ];

        // Determine type
        if ($mapped === 'section') {
            $element['elType'] = 'section';
        } elseif ($mapped === 'column') {
            $element['elType'] = 'column';
        } else {
            $element['elType'] = 'widget';
            $element['widgetType'] = $mapped;

            // Basic content extraction
            if ($mapped === 'text-editor') {
                $element['settings']['editor'] = strip_tags($node['raw']);
            }
        }

        // Children
        foreach ($node['children'] as $child) {
            $element['elements'][] = $this->node($child);
        }

        return $element;
    }
}