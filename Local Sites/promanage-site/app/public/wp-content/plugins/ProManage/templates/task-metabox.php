<?php
/**
 * Task Metabox Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<table class="form-table">
    <tr>
        <th scope="row">
            <label for="promanage_due_date"><?php _e('Due Date', 'promanage'); ?></label>
        </th>
        <td>
            <input type="date" id="promanage_due_date" name="promanage_due_date" value="<?php echo esc_attr($due_date); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="promanage_priority"><?php _e('Priority', 'promanage'); ?></label>
        </th>
        <td>
            <select id="promanage_priority" name="promanage_priority">
                <option value="low" <?php selected($priority, 'low'); ?>><?php _e('Low', 'promanage'); ?></option>
                <option value="medium" <?php selected($priority, 'medium'); ?>><?php _e('Medium', 'promanage'); ?></option>
                <option value="high" <?php selected($priority, 'high'); ?>><?php _e('High', 'promanage'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="promanage_status"><?php _e('Status', 'promanage'); ?></label>
        </th>
        <td>
            <select id="promanage_status" name="promanage_status">
                <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'promanage'); ?></option>
                <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'promanage'); ?></option>
                <option value="done" <?php selected($status, 'done'); ?>><?php _e('Done', 'promanage'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="promanage_project_id"><?php _e('Project', 'promanage'); ?></label>
        </th>
        <td>
            <select id="promanage_project_id" name="promanage_project_id">
                <option value=""><?php _e('-- Select Project --', 'promanage'); ?></option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo $project->ID; ?>" <?php selected($project_id, $project->ID); ?>>
                        <?php echo esc_html($project->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="promanage_assigned_user"><?php _e('Assign To', 'promanage'); ?></label>
        </th>
        <td>
            <select id="promanage_assigned_user" name="promanage_assigned_user">
                <option value=""><?php _e('-- Select User --', 'promanage'); ?></option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user->ID; ?>" <?php selected($assigned_user, $user->ID); ?>>
                        <?php echo esc_html($user->display_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>
