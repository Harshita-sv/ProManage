<?php
/**
 * Core ProManage Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class ProManage_Core {
    
    public function init() {
        add_action('init', array($this, 'load_textdomain'));
        
        // Initialize components
        $this->init_post_types();
        $this->init_admin();
        $this->init_ajax();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('promanage', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    private function init_post_types() {
        $post_types = new ProManage_Post_Types();
        $post_types->init();
    }
    
    private function init_admin() {
        if (is_admin()) {
            $admin = new ProManage_Admin();
            $admin->init();
        }
    }
    
    private function init_ajax() {
        $ajax = new ProManage_Ajax();
        $ajax->init();
    }
}