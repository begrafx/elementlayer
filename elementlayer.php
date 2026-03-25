<?php
/**
 * Plugin Name: ElementLayer
 * Plugin URI: https://github.com/begrafx/elementlayer
 * Description: Convert Pagelayer pages, posts, and custom post types into Elementor drafts safely.
 * Version: 0.1
 * Author: BeGrafx
 * Author URI: https://github.com/begrafx
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('EL_VERSION', '0.1');
define('EL_PATH', plugin_dir_path(__FILE__));
define('EL_URL', plugin_dir_url(__FILE__));

// --------------------------------------------------
// Load Composer Autoloader (Required)
// --------------------------------------------------

$autoload = EL_PATH . 'vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><strong>ElementLayer:</strong> Missing dependencies. Please run <code>composer install</code>.</p></div>';
    });
    return;
}

// --------------------------------------------------
// GitHub Updater Setup
// --------------------------------------------------

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/begrafx/elementlayer/',
    __FILE__,
    'elementlayer'
);

// Use GitHub Releases (recommended)
$updateChecker->getVcsApi()->enableReleaseAssets();

// Optional: If repo becomes private later
// $updateChecker->setAuthentication('YOUR_GITHUB_TOKEN');

// --------------------------------------------------
// Load Core Plugin Classes
// --------------------------------------------------

require_once EL_PATH . 'includes/Parser.php';
require_once EL_PATH . 'includes/Mapper.php';
require_once EL_PATH . 'includes/Converter.php';
require_once EL_PATH . 'includes/Admin.php';

// --------------------------------------------------
// Initialize Plugin
// --------------------------------------------------

function elementlayer_init() {
    new EL_Admin();
}

add_action('plugins_loaded', 'elementlayer_init');
