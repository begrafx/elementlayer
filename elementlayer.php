<?php
/**
 * Plugin Name: ElementLayer (Alpha)
 * Description: Convert Pagelayer pages to Elementor safely.
 * Version: 0.1-alpha
 */

if (!defined('ABSPATH')) exit;

define('EL_PATH', plugin_dir_path(__FILE__));

require_once EL_PATH . 'includes/Parser.php';
require_once EL_PATH . 'includes/Mapper.php';
require_once EL_PATH . 'includes/Converter.php';
require_once EL_PATH . 'includes/Admin.php';

new EL_Admin();