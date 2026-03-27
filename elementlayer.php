<?php
/**
 * Plugin Name: ElementLayer
 * Plugin URI: https://github.com/begrafx/elementlayer
 * Description: Convert Pagelayer pages, posts, and custom post types into Elementor drafts safely.
 * Version: 0.1
 * Author: Brian Eller
 * Author URI: https://github.com/begrafx
 */

if (!defined('ABSPATH')) {
    exit;
}

// --------------------------------------------------
// Constants
// --------------------------------------------------

define('EL_VERSION', '0.1');
define('EL_PATH', plugin_dir_path(__FILE__));
define('EL_URL', plugin_dir_url(__FILE__));

// --------------------------------------------------
// Composer Autoload
// --------------------------------------------------

$autoload = EL_PATH . 'vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><strong>ElementLayer:</strong> Missing dependencies. Run composer install or use release ZIP.</p></div>';
    });
    return;
}

// --------------------------------------------------
// Includes
// --------------------------------------------------

require_once EL_PATH . 'includes/Parser.php';
require_once EL_PATH . 'includes/Mapper.php';
require_once EL_PATH . 'includes/Converter.php';
require_once EL_PATH . 'includes/Admin.php';

// --------------------------------------------------
// Plugin Init
// --------------------------------------------------

function elementlayer_init() {
    new EL_Admin();
}

add_action('plugins_loaded', 'elementlayer_init');

// --------------------------------------------------
// GitHub Updater (FIXED + SAFE)
// --------------------------------------------------

add_action('plugins_loaded', function () {

    if (!class_exists('\YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
        return;
    }

    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/begrafx/elementlayer',
        __FILE__,
        'elementlayer'
    );

    $updateChecker->setBranch('main');
    $updateChecker->getVcsApi()->enableReleaseAssets();

});

// --------------------------------------------------
// ⚠️ TEMPORARY DEBUG ONLY (REMOVE AFTER TESTING)
// --------------------------------------------------

add_action('admin_init', function () {
    delete_site_transient('update_plugins');
});