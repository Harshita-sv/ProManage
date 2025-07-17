<?php
/**
 * AJAX handlers for ProManage
 */

if (!defined('ABSPATH')) {
    exit;
}

class ProManage_Ajax {
    
    public function init() {
        add_action('wp_ajax_promanage_update_task_status', array($this, 'update_task_status'));
        add_action('wp_ajax_promanage_filter_tasks', array($this, 'filter_tasks'));
    }
    
    public function update_task_status() {
        check_ajax_referer('promanage_ajax_nonce', 'nonce');
        
        $task_id = intval($_POST['task_id']);
        $new_status = sanitize_text_field($_POST['status']);
        
        $allowed_statuses = array('pending', 'in_progress', 'done');
        
        if (!in_array($new_status, $allowed_statuses)) {
            wp_die('Invalid status');
        }
        
        update_post_meta($task_id, '_promanage_status', $new_status);
        
        wp_send_json_success(array(
            'message' => 'Task status updated successfully'
        ));
    }
    
    public function filter_tasks() {
        check_ajax_referer('promanage_ajax_nonce', 'nonce');
        
        $status = sanitize_text_field($_POST['status']);
        $user_id = intval($_POST['user_id']);
        $project_id = intval($_POST['project_id']);
        
        $meta_query = array();
        
        if ($status && $status !== 'all') {
            $meta_query[] = array(
                'key' => '_promanage_status',
                'value' => $status,
                'compare' => '='
            );
        }
        
        if ($user_id) {
            $meta_query[] = array(
                'key' => '_promanage_assigned_user',
                'value' => $user_id,
                'compare' => '='
            );
        }
        
        if ($project_id) {
            $meta_query[] = array(
                'key' => '_promanage_project_id',
                'value' => $project_id,
                'compare' => '='
            );
        }
        
        $tasks = get_posts(array(
            'post_type' => 'promanage_task',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => $meta_query
        ));
        
        $html = '';
        foreach ($tasks as $task) {
            $meta = array(
                'due_date' => get_post_meta($task->ID, '_promanage_due_date', true),
                'priority' => get_post_meta($task->ID, '_promanage_priority', true),
                'status' => get_post_meta($task->ID, '_promanage_status', true),
                'assigned_user' => get_post_meta($task->ID, '_promanage_assigned_user', true)
            );
            
            $user = get_user_by('id', $meta['assigned_user']);
            $user_name = $user ? $user->display_name : 'Unassigned';
            
            $status_class = 'promanage-status-' . $meta['status'];
            $priority_class = 'promanage-priority-' . $meta['priority'];
            
            $html .= '<div class="promanage-task-card ' . $status_class . '">';
            $html .= '<h4>' . esc_html($task->post_title) . '</h4>';
            $html .= '<p>' . esc_html($task->post_content) . '</p>';
            $html .= '<div class="promanage-task-meta">';
            $html .= '<span class="promanage-badge promanage-status-badge ' . $status_class . '">' . ucfirst($meta['status']) . '</span>';
            $html .= '<span class="promanage-badge promanage-priority-badge ' . $priority_class . '">' . ucfirst($meta['priority']) . '</span>';
            $html .= '<span class="promanage-assigned">Assigned to: ' . esc_html($user_name) . '</span>';
            $html .= '<span class="promanage-due-date">Due: ' . esc_html($meta['due_date']) . '</span>';
            $html .= '</div>';
            $html .= '<div class="promanage-task-actions">';
            $html .= '<button class="promanage-btn promanage-btn-sm promanage-update-status" data-task-id="' . $task->ID . '" data-status="pending">Pending</button>';
            $html .= '<button class="promanage-btn promanage-btn-sm promanage-update-status" data-task-id="' . $task->ID . '" data-status="in_progress">In Progress</button>';
            $html .= '<button class="promanage-btn promanage-btn-sm promanage-update-status" data-task-id="' . $task->ID . '" data-status="done">Done</button>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        wp_send_json_success(array(
            'html' => $html
        ));
    }
}