<?php
/**
 * Plugin Name: ElementLayer
 * Plugin URI: https://github.com/begrafx/elementlayer
 * Description: Convert Pagelayer pages, posts, and custom post types into Elementor drafts safely.
 * Version: 0.1
 * Author: Brian Eller
 * Author URI: https://github.com/begrafx
 */


/** v0.2.1 Version update test push. */

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
// Load Composer Autoloader
// --------------------------------------------------

$autoload = EL_PATH . 'vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error">';
        echo '<p><strong>ElementLayer Error:</strong> Missing dependencies.</p>';
        echo '<p>Please run <code>composer install</code> or install a proper release package.</p>';
        echo '</div>';
    });
    return;
}

// --------------------------------------------------
// GitHub Updater (FIXED + HARDENED)
// --------------------------------------------------

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/begrafx/elementlayer', // ✅ no trailing slash
    __FILE__,
    'elementlayer'
);

// 🔥 Critical fixes
$updateChecker->setBranch('main');
$updateChecker->getVcsApi()->enableReleaseAssets();

// --------------------------------------------------
// TEMP: Force Update Check (REMOVE AFTER TESTING)
// --------------------------------------------------

add_action('admin_init', function () {
    delete_site_transient('update_plugins');
});

// --------------------------------------------------
// Load Core Classes
// --------------------------------------------------

require_once EL_PATH . 'includes/Parser.php';
require_once EL_PATH . 'includes/Mapper.php';
require_once EL_PATH . 'includes/Converter.php';
require_once EL_PATH . 'includes/Admin.php';

// --------------------------------------------------
// Init Plugin
// --------------------------------------------------

function elementlayer_init() {
    new EL_Admin();
}

add_action('plugins_loaded', 'elementlayer_init');