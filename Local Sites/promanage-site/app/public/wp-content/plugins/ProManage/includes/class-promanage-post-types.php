<?php
/**
 * Custom Post Types for ProManage
 */

if (!defined('ABSPATH')) {
    exit;
}

class ProManage_Post_Types {
    
    public function init() {
        add_action('init', array($this, 'register_post_types'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }
    
    public function register_post_types() {
        // Register Project post type
        register_post_type('promanage_project', array(
            'label' => __('Projects', 'promanage'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'supports' => array('title', 'editor'),
            'labels' => array(
                'name' => __('Projects', 'promanage'),
                'singular_name' => __('Project', 'promanage'),
                'add_new' => __('Add New Project', 'promanage'),
                'add_new_item' => __('Add New Project', 'promanage'),
                'edit_item' => __('Edit Project', 'promanage'),
                'new_item' => __('New Project', 'promanage'),
                'view_item' => __('View Project', 'promanage'),
                'search_items' => __('Search Projects', 'promanage'),
                'not_found' => __('No projects found', 'promanage'),
                'not_found_in_trash' => __('No projects found in trash', 'promanage')
            )
        ));
        
        // Register Task post type
        register_post_type('promanage_task', array(
            'label' => __('Tasks', 'promanage'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'supports' => array('title', 'editor'),
            'labels' => array(
                'name' => __('Tasks', 'promanage'),
                'singular_name' => __('Task', 'promanage'),
                'add_new' => __('Add New Task', 'promanage'),
                'add_new_item' => __('Add New Task', 'promanage'),
                'edit_item' => __('Edit Task', 'promanage'),
                'new_item' => __('New Task', 'promanage'),
                'view_item' => __('View Task', 'promanage'),
                'search_items' => __('Search Tasks', 'promanage'),
                'not_found' => __('No tasks found', 'promanage'),
                'not_found_in_trash' => __('No tasks found in trash', 'promanage')
            )
        ));
    }
    
    public function add_meta_boxes() {
        // Project meta boxes
        add_meta_box(
            'promanage_project_details',
            __('Project Details', 'promanage'),
            array($this, 'project_meta_box_callback'),
            'promanage_project',
            'normal',
            'default'
        );
        
        // Task meta boxes
        add_meta_box(
            'promanage_task_details',
            __('Task Details', 'promanage'),
            array($this, 'task_meta_box_callback'),
            'promanage_task',
            'normal',
            'default'
        );
    }
    
    public function project_meta_box_callback($post) {
        wp_nonce_field('promanage_project_meta_box', 'promanage_project_meta_box_nonce');
        
        $start_date = get_post_meta($post->ID, '_promanage_start_date', true);
        $end_date = get_post_meta($post->ID, '_promanage_end_date', true);
        
        include PROMANAGE_PLUGIN_DIR . 'templates/project-metabox.php';
    }
    
    public function task_meta_box_callback($post) {
        wp_nonce_field('promanage_task_meta_box', 'promanage_task_meta_box_nonce');
        
        $due_date = get_post_meta($post->ID, '_promanage_due_date', true);
        $priority = get_post_meta($post->ID, '_promanage_priority', true);
        $status = get_post_meta($post->ID, '_promanage_status', true);
        $project_id = get_post_meta($post->ID, '_promanage_project_id', true);
        $assigned_user = get_post_meta($post->ID, '_promanage_assigned_user', true);
        
        // Get projects for dropdown
        $projects = get_posts(array(
            'post_type' => 'promanage_project',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        // Get users for dropdown
        $users = get_users();
        
        include PROMANAGE_PLUGIN_DIR . 'templates/task-metabox.php';
    }
    
    public function save_meta_boxes($post_id) {
        // Save project meta
        if (isset($_POST['promanage_project_meta_box_nonce']) && 
            wp_verify_nonce($_POST['promanage_project_meta_box_nonce'], 'promanage_project_meta_box')) {
            
            if (isset($_POST['promanage_start_date'])) {
                update_post_meta($post_id, '_promanage_start_date', sanitize_text_field($_POST['promanage_start_date']));
            }
            
            if (isset($_POST['promanage_end_date'])) {
                update_post_meta($post_id, '_promanage_end_date', sanitize_text_field($_POST['promanage_end_date']));
            }
        }
        
        // Save task meta
        if (isset($_POST['promanage_task_meta_box_nonce']) && 
            wp_verify_nonce($_POST['promanage_task_meta_box_nonce'], 'promanage_task_meta_box')) {
            
            if (isset($_POST['promanage_due_date'])) {
                update_post_meta($post_id, '_promanage_due_date', sanitize_text_field($_POST['promanage_due_date']));
            }
            
            if (isset($_POST['promanage_priority'])) {
                update_post_meta($post_id, '_promanage_priority', sanitize_text_field($_POST['promanage_priority']));
            }
            
            if (isset($_POST['promanage_status'])) {
                update_post_meta($post_id, '_promanage_status', sanitize_text_field($_POST['promanage_status']));
            }
            
            if (isset($_POST['promanage_project_id'])) {
                update_post_meta($post_id, '_promanage_project_id', intval($_POST['promanage_project_id']));
            }
            
            if (isset($_POST['promanage_assigned_user'])) {
                update_post_meta($post_id, '_promanage_assigned_user', intval($_POST['promanage_assigned_user']));
            }
        }
    }
}