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
        echo '<p>Select a Pagelayer-built item to convert into an Elementor draft.</p>';

        echo '<form method="post">';
        echo '<select name="post_id">';

        // 🔥 NEW IMPROVED QUERY
        $post_types = get_post_types([
            'public' => true,
        ], 'objects');

        foreach ($post_types as $type) {

            if (in_array($type->name, ['attachment', 'elementor_library'])) {
                continue;
            }

            $posts = get_posts([
                'post_type' => $type->name,
                'numberposts' => 50,
            ]);

            if (!$posts) continue;

            echo "<optgroup label='{$type->labels->name}'>";

            foreach ($posts as $post) {

                if (strpos($post->post_content, 'pagelayer-') === false) {
                    continue;
                }

                $title = esc_html($post->post_title ?: '(No Title)');
                echo "<option value='{$post->ID}'>{$title}</option>";
            }

            echo "</optgroup>";
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

        $new_id = wp_insert_post([
            'post_title' => $original->post_title . ' (Elementor Copy)',
            'post_status' => 'draft',
            'post_type' => $original->post_type,
        ]);

        update_post_meta($new_id, '_elementor_data', wp_json_encode($elementor));
        update_post_meta($new_id, '_elementor_edit_mode', 'builder');
        update_post_meta($new_id, '_elementor_version', '3.0');

        echo "<div class='updated'><p>Converted! New draft ID: {$new_id}</p></div>";
        echo "<p><a href='" . get_edit_post_link($new_id) . "'>Edit in Elementor</a></p>";
    }
}