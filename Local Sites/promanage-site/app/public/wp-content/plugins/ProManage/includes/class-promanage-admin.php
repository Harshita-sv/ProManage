<?php
/**
 * Admin functionality for ProManage
 */

if (!defined('ABSPATH')) {
    exit;
}

class ProManage_Admin {
    
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('ProManage', 'promanage'),
            __('ProManage', 'promanage'),
            'manage_options',
            'promanage',
            array($this, 'admin_dashboard'),
            'dashicons-clipboard',
            30
        );
        
        add_submenu_page(
            'promanage',
            __('Dashboard', 'promanage'),
            __('Dashboard', 'promanage'),
            'manage_options',
            'promanage',
            array($this, 'admin_dashboard')
        );
        
        add_submenu_page(
            'promanage',
            __('Projects', 'promanage'),
            __('Projects', 'promanage'),
            'manage_options',
            'edit.php?post_type=promanage_project'
        );
        
        add_submenu_page(
            'promanage',
            __('Tasks', 'promanage'),
            __('Tasks', 'promanage'),
            'manage_options',
            'edit.php?post_type=promanage_task'
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'promanage') !== false) {
            wp_enqueue_style('promanage-admin-style', PROMANAGE_PLUGIN_URL . 'assets/css/admin-style.css', array(), PROMANAGE_VERSION);
            wp_enqueue_script('promanage-admin-script', PROMANAGE_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), PROMANAGE_VERSION, true);
            
            // Localize script for AJAX
            wp_localize_script('promanage-admin-script', 'promanage_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('promanage_ajax_nonce')
            ));
            
            // Pass chart data to JavaScript
            $task_stats = $this->get_task_statistics();
            wp_localize_script('promanage-admin-script', 'promanage_chart_data', $task_stats);
        }
    }
    
    public function admin_dashboard() {
        $projects = $this->get_projects_with_tasks();
        $task_stats = $this->get_task_statistics();
        
        include PROMANAGE_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    private function get_projects_with_tasks() {
        $projects = get_posts(array(
            'post_type' => 'promanage_project',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        foreach ($projects as $project) {
            $project->tasks = get_posts(array(
                'post_type' => 'promanage_task',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_promanage_project_id',
                        'value' => $project->ID,
                        'compare' => '='
                    )
                )
            ));
            
            // Add meta data to tasks
            foreach ($project->tasks as $task) {
                $task->meta = array(
                    'due_date' => get_post_meta($task->ID, '_promanage_due_date', true),
                    'priority' => get_post_meta($task->ID, '_promanage_priority', true),
                    'status' => get_post_meta($task->ID, '_promanage_status', true),
                    'assigned_user' => get_post_meta($task->ID, '_promanage_assigned_user', true)
                );
            }
        }
        
        return $projects;
    }
    
    private function get_task_statistics() {
        $tasks = get_posts(array(
            'post_type' => 'promanage_task',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $stats = array(
            'done' => 0,
            'in_progress' => 0,
            'pending' => 0
        );
        
        foreach ($tasks as $task) {
            $status = get_post_meta($task->ID, '_promanage_status', true);
            if ($status === 'done') {
                $stats['done']++;
            } elseif ($status === 'in_progress') {
                $stats['in_progress']++;
            } else {
                $stats['pending']++;
            }
        }
        
        return $stats;
    }
}