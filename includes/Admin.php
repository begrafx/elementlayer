<?php

class EL_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu() {
        add_menu_page(
            'ElementLayer',
            'ElementLayer',
            'manage_options',
            'elementlayer',
            [$this, 'page'],
            'dashicons-update'
        );
    }

    public function page() {

        if (isset($_POST['el_convert'])) {
            $this->convert((int) $_POST['post_id']);
        }

        echo '<div class="wrap">';
        echo '<h1>ElementLayer (Alpha)</h1>';

        echo '<form method="post">';
        echo '<select name="post_id">';

        $posts = get_posts(['numberposts' => 50]);

        foreach ($posts as $post) {
            echo "<option value='{$post->ID}'>{$post->post_title}</option>";
        }

        echo '</select>';
        echo '<br><br>';
        echo '<button class="button button-primary" name="el_convert">Convert to Elementor Draft</button>';
        echo '</form>';

        echo '</div>';
    }

    private function convert($post_id) {

        $original = get_post($post_id);
        if (!$original) return;

        $parser = new EL_Parser();
        $converter = new EL_Converter();

        $parsed = $parser->parse($original->post_content);
        $elementor = $converter->convert($parsed);

        // Create NEW draft
        $new_id = wp_insert_post([
            'post_title' => $original->post_title . ' (Elementor Copy)',
            'post_status' => 'draft',
            'post_type' => $original->post_type,
        ]);

        // Save Elementor data
        update_post_meta($new_id, '_elementor_data', wp_json_encode($elementor));
        update_post_meta($new_id, '_elementor_edit_mode', 'builder');
        update_post_meta($new_id, '_elementor_version', '3.0');

        echo "<div class='updated'><p>Converted! New draft ID: {$new_id}</p></div>";
    }
}