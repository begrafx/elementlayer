<?php

class EL_Parser {

    public function parse($content) {
        return $this->parse_nodes($content);
    }

    private function parse_nodes($content) {
        $pattern = '/\[pagelayer-(.*?)\](.*?)\[\/pagelayer-\1\]/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $nodes = [];

        foreach ($matches as $match) {
            $nodes[] = [
                'type' => 'pagelayer-' . $match[1],
                'raw' => $match[2],
                'children' => $this->parse_nodes($match[2])
            ];
        }

        return $nodes;
    }
}