<?php
/**
 * Admin Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap promanage-wrap">
    <h1 class="promanage-title"><?php _e('ProManage Dashboard', 'promanage'); ?></h1>
    
    <div class="promanage-dashboard-header">
        <div class="promanage-stats-grid">
            <div class="promanage-stat-card">
                <h3><?php echo count($projects); ?></h3>
                <p>Total Projects</p>
            </div>
            <div class="promanage-stat-card">
                <h3><?php echo $task_stats['done']; ?></h3>
                <p>Completed Tasks</p>
            </div>
            <div class="promanage-stat-card">
                <h3><?php echo $task_stats['in_progress']; ?></h3>
                <p>In Progress</p>
            </div>
            <div class="promanage-stat-card">
                <h3><?php echo $task_stats['pending']; ?></h3>
                <p>Pending Tasks</p>
            </div>
        </div>
        
        <div class="promanage-chart-container">
            <h3><?php _e('Task Status Overview', 'promanage'); ?></h3>
            <canvas id="taskChart" width="300" height="300"></canvas>
        </div>
    </div>
    
    <div class="promanage-filters">
        <h3><?php _e('Filter Tasks', 'promanage'); ?></h3>
        <div class="promanage-filter-row">
            <select id="status-filter">
                <option value="all"><?php _e('All Statuses', 'promanage'); ?></option>
                <option value="pending"><?php _e('Pending', 'promanage'); ?></option>
                <option value="in_progress"><?php _e('In Progress', 'promanage'); ?></option>
                <option value="done"><?php _e('Done', 'promanage'); ?></option>
            </select>
            
            <select id="user-filter">
                <option value=""><?php _e('All Users', 'promanage'); ?></option>
                <?php
                $users = get_users();
                foreach ($users as $user) {
                    echo '<option value="' . $user->ID . '">' . esc_html($user->display_name) . '</option>';
                }
                ?>
            </select>
            
            <select id="project-filter">
                <option value=""><?php _e('All Projects', 'promanage'); ?></option>
                <?php
                foreach ($projects as $project) {
                    echo '<option value="' . $project->ID . '">' . esc_html($project->post_title) . '</option>';
                }
                ?>
            </select>
            
            <button id="filter-tasks" class="promanage-btn promanage-btn-primary"><?php _e('Filter', 'promanage'); ?></button>
        </div>
    </div>
    
    <div class="promanage-projects-container">
        <h3><?php _e('Projects & Tasks', 'promanage'); ?></h3>
        
        <div id="promanage-tasks-container">
            <?php foreach ($projects as $project): ?>
                <div class="promanage-project-card">
                    <h3 class="promanage-project-title"><?php echo esc_html($project->post_title); ?></h3>
                    <p class="promanage-project-description"><?php echo esc_html($project->post_content); ?></p>
                    
                    <div class="promanage-project-meta">
                        <span>Start: <?php echo esc_html(get_post_meta($project->ID, '_promanage_start_date', true)); ?></span>
                        <span>End: <?php echo esc_html(get_post_meta($project->ID, '_promanage_end_date', true)); ?></span>
                    </div>
                    
                    <div class="promanage-tasks-grid">
                        <?php foreach ($project->tasks as $task): ?>
                            <?php 
                            $user = get_user_by('id', $task->meta['assigned_user']);
                            $user_name = $user ? $user->display_name : 'Unassigned';
                            $status_class = 'promanage-status-' . $task->meta['status'];
                            $priority_class = 'promanage-priority-' . $task->meta['priority'];
                            ?>
                            <div class="promanage-task-card <?php echo $status_class; ?>">
                                <h4><?php echo esc_html($task->post_title); ?></h4>
                                <p><?php echo esc_html($task->post_content); ?></p>
                                
                                <div class="promanage-task-meta">
                                    <span class="promanage-badge promanage-status-badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($task->meta['status']); ?>
                                    </span>
                                    <span class="promanage-badge promanage-priority-badge <?php echo $priority_class; ?>">
                                        <?php echo ucfirst($task->meta['priority']); ?>
                                    </span>
                                    <span class="promanage-assigned">Assigned to: <?php echo esc_html($user_name); ?></span>
                                    <span class="promanage-due-date">Due: <?php echo esc_html($task->meta['due_date']); ?></span>
                                </div>
                                
                                <div class="promanage-task-actions">
                                    <button class="promanage-btn promanage-btn-sm promanage-update-status" 
                                            data-task-id="<?php echo $task->ID; ?>" 
                                            data-status="pending">Pending</button>
                                    <button class="promanage-btn promanage-btn-sm promanage-update-status" 
                                            data-task-id="<?php echo $task->ID; ?>" 
                                            data-status="in_progress">In Progress</button>
                                    <button class="promanage-btn promanage-btn-sm promanage-update-status" 
                                            data-task-id="<?php echo $task->ID; ?>" 
                                            data-status="done">Done</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>