<?php
/**
 * Project Metabox Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<table class="form-table">
    <tr>
        <th scope="row">
            <label for="promanage_start_date"><?php _e('Start Date', 'promanage'); ?></label>
        </th>
        <td>
            <input type="date" id="promanage_start_date" name="promanage_start_date" value="<?php echo esc_attr($start_date); ?>" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="promanage_end_date"><?php _e('End Date', 'promanage'); ?></label>
        </th>
        <td>
            <input type="date" id="promanage_end_date" name="promanage_end_date" value="<?php echo esc_attr($end_date); ?>" />
        </td>
    </tr>
</table>