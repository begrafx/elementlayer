<?php

class EL_Mapper {

    private $map = [
        'pagelayer-row' => 'section',
        'pagelayer-col' => 'column',
        'pagelayer-text' => 'text-editor',
        'pagelayer-heading' => 'heading',
        'pagelayer-image' => 'image',
        'pagelayer-button' => 'button',
    ];

    public function get($type) {
        return $this->map[$type] ?? 'html';
    }
}