<?php
/**
 * Plugin Name: ProManage - Project & Task Manager
 * Description: A comprehensive project and task management system for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: promanage
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PROMANAGE_VERSION', '1.0.0');
define('PROMANAGE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PROMANAGE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once PROMANAGE_PLUGIN_DIR . 'includes/class-promanage-core.php';
require_once PROMANAGE_PLUGIN_DIR . 'includes/class-promanage-post-types.php';
require_once PROMANAGE_PLUGIN_DIR . 'includes/class-promanage-admin.php';
require_once PROMANAGE_PLUGIN_DIR . 'includes/class-promanage-ajax.php';

// Initialize the plugin
function promanage_init() {
    $promanage = new ProManage_Core();
    $promanage->init();
}
add_action('plugins_loaded', 'promanage_init');

// Activation hook
register_activation_hook(__FILE__, 'promanage_activate');
function promanage_activate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'promanage_deactivate');
function promanage_deactivate() {
    flush_rewrite_rules();
}