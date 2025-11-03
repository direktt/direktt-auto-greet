<?php

/**
 * Plugin Name: Direktt Auto Greet - Welcome + Out of Office Automated Messages
 * Description: Direktt Auto Greet - Welcome + Out of Office Automated Messages
 * Version: 1.0.1
 * Author: Direktt
 * Author URI: https://direktt.com/
 * License: GPL2
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

$direktt_auto_greet_plugin_version = "1.0.1";
$direktt_auto_greet_github_update_cache_allowed = true;

require_once plugin_dir_path( __FILE__ ) . 'direktt-github-updater/class-direktt-github-updater.php';

$direktt_auto_greet_plugin_github_updater  = new Direktt_Github_Updater( 
    $direktt_auto_greet_plugin_version, 
    'direktt-auto-greet/direktt-auto-greet.php',
    'https://raw.githubusercontent.com/direktt/direktt-auto-greet/master/info.json',
    'direktt_auto_greet_github_updater',
    $direktt_auto_greet_github_update_cache_allowed );

add_filter( 'plugins_api', array( $direktt_auto_greet_plugin_github_updater, 'github_info' ), 20, 3 );
add_filter( 'site_transient_update_plugins', array( $direktt_auto_greet_plugin_github_updater, 'github_update' ));
add_filter( 'upgrader_process_complete', array( $direktt_auto_greet_plugin_github_updater, 'purge'), 10, 2 );

add_action('plugins_loaded', 'direktt_auto_greet_activation_check', -20);

function direktt_auto_greet_activation_check()
{
    if (! function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $required_plugin = 'direktt/direktt.php';
    $is_required_active = is_plugin_active($required_plugin)
        || (is_multisite() && is_plugin_active_for_network($required_plugin));

    if (! $is_required_active) {
        // Deactivate this plugin
        deactivate_plugins(plugin_basename(__FILE__));

        // Prevent the “Plugin activated.” notice
        if (isset($_GET['activate'])) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Justification: not a form processing, just removing a query var.
            unset($_GET['activate']);
        }

        // Show an error notice for this request
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error is-dismissible"><p>'
                . esc_html__('Direktt Auto Greet activation failed: The Direktt WordPress Plugin must be active first.', 'direktt-auto-greet')
                . '</p></div>';
        });

        // Optionally also show the inline row message in the plugins list
        add_action(
            'after_plugin_row_direktt-auto-greet/direktt-auto-greet.php',
            function () {
                echo '<tr class="plugin-update-tr"><td colspan="3" style="box-shadow:none;">'
                    . '<div style="color:#b32d2e;font-weight:bold;">'
                    . esc_html__('Direktt Auto Greet requires the Direktt WordPress Plugin to be active. Please activate it first.', 'direktt-auto-greet')
                    . '</div></td></tr>';
            },
            10,
            0
        );
    }
}

add_action('direktt_setup_settings_pages', 'direktt_auto_greet_setup_settings_pages');

function direktt_auto_greet_setup_settings_pages()
{

    Direktt::add_settings_page(
        array(
            'id'       => 'welcome-message',
            'label'    => esc_html__('Auto Greet Settings', 'direktt-auto-greet'),
            'callback' => 'direktt_auto_greet_render_welcome_settings',
            'priority' => 1,
        )
    );
}

function direktt_auto_greet_render_welcome_settings()
{
    // Success message flag
    $success = false;

    // Handle form submission
    if (
        isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['direktt_admin_welcome_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['direktt_admin_welcome_nonce'])), 'direktt_admin_welcome_save')
    ) {
        // Sanitize and update options
        update_option('direktt_welcome_user', isset($_POST['direktt_welcome_user']) ? 'yes' : 'no');
        update_option('direktt_welcome_user_template', isset($_POST['direktt_welcome_user_template']) ? intval($_POST['direktt_welcome_user_template']) : 0);
        update_option('direktt_welcome_admin', isset($_POST['direktt_welcome_admin']) ? 'yes' : 'no');
        update_option('direktt_welcome_admin_template', isset($_POST['direktt_welcome_admin_template']) ? intval($_POST['direktt_welcome_admin_template']) : 0);
        update_option('direktt_auto_greet_mode', isset($_POST['direktt_auto_greet_mode']) ? sanitize_text_field(wp_unslash($_POST['direktt_auto_greet_mode'])) : '');
        update_option('direktt_auto_greet_always_template', isset($_POST['direktt_auto_greet_always_template']) ? intval($_POST['direktt_auto_greet_always_template']) : 0);
        update_option('direktt_auto_greet_non_working_template', isset($_POST['direktt_auto_greet_non_working_template']) ? intval($_POST['direktt_auto_greet_non_working_template']) : 0);
        update_option(
            'direktt_auto_greet_working_hours',
            array(
                'monday'    => array(
                    'closed' => isset($_POST['monday_closed']) ? true : false,
                    'start'  => isset($_POST['monday_start']) ? sanitize_text_field(wp_unslash($_POST['monday_start'])) : '',
                    'end'    => isset($_POST['monday_end']) ? sanitize_text_field(wp_unslash($_POST['monday_end'])) : '',
                ),
                'tuesday'   => array(
                    'closed' => isset($_POST['tuesday_closed']) ? true : false,
                    'start'  => isset($_POST['tuesday_start']) ? sanitize_text_field(wp_unslash($_POST['tuesday_start'])) : '',
                    'end'    => isset($_POST['tuesday_end']) ? sanitize_text_field(wp_unslash($_POST['tuesday_end'])) : '',
                ),
                'wednesday' => array(
                    'closed' => isset($_POST['wednesday_closed']) ? true : false,
                    'start'  => isset($_POST['wednesday_start']) ? sanitize_text_field(wp_unslash($_POST['wednesday_start'])) : '',
                    'end'    => isset($_POST['wednesday_end']) ? sanitize_text_field(wp_unslash($_POST['wednesday_end'])) : '',
                ),
                'thursday'  => array(
                    'closed' => isset($_POST['thursday_closed']) ? true : false,
                    'start'  => isset($_POST['thursday_start']) ? sanitize_text_field(wp_unslash($_POST['thursday_start'])) : '',
                    'end'    => isset($_POST['thursday_end']) ? sanitize_text_field(wp_unslash($_POST['thursday_end'])) : '',
                ),
                'friday'    => array(
                    'closed' => isset($_POST['friday_closed']) ? true : false,
                    'start'  => isset($_POST['friday_start']) ? sanitize_text_field(wp_unslash($_POST['friday_start'])) : '',
                    'end'    => isset($_POST['friday_end']) ? sanitize_text_field(wp_unslash($_POST['friday_end'])) : '',
                ),
                'saturday'  => array(
                    'closed' => isset($_POST['saturday_closed']) ? true : false,
                    'start'  => isset($_POST['saturday_start']) ? sanitize_text_field(wp_unslash($_POST['saturday_start'])) : '',
                    'end'    => isset($_POST['saturday_end']) ? sanitize_text_field(wp_unslash($_POST['saturday_end'])) : '',
                ),
                'sunday'    => array(
                    'closed' => isset($_POST['sunday_closed']) ? true : false,
                    'start'  => isset($_POST['sunday_start']) ? sanitize_text_field(wp_unslash($_POST['sunday_start'])) : '',
                    'end'    => isset($_POST['sunday_end']) ? sanitize_text_field(wp_unslash($_POST['sunday_end'])) : '',
                ),
            )
        );
        $success = true;
    }

    // Load stored values
    $welcome_user             = get_option('direktt_welcome_user', 'no') === 'yes';
    $welcome_user_template    = intval(get_option('direktt_welcome_user_template', 0));
    $welcome_admin            = get_option('direktt_welcome_admin', 'no') === 'yes';
    $welcome_admin_template   = intval(get_option('direktt_welcome_admin_template', 0));
    $ooo_mode                 = get_option('direktt_auto_greet_mode', 'off');
    $ooo_always_template      = intval(get_option('direktt_auto_greet_always_template', 0));
    $ooo_non_working_template = intval(get_option('direktt_auto_greet_non_working_template', 0));
    $ooo_working_hours        = get_option(
        'direktt_auto_greet_working_hours',
        array(
            'monday'    => array(
                'closed' => false,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'tuesday'   => array(
                'closed' => false,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'wednesday' => array(
                'closed' => false,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'thursday'  => array(
                'closed' => false,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'friday'    => array(
                'closed' => false,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'saturday'  => array(
                'closed' => true,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
            'sunday'    => array(
                'closed' => true,
                'start'  => '09:00',
                'end'    => '17:00',
            ),
        )
    );

    // Query for template posts
    $template_args  = array(
        'post_type'      => 'direkttmtemplates',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- - Justification: bounded, cached, selective query on small dataset
            array(
                'key'     => 'direkttMTType',
                'value'   => array('all', 'none'),
                'compare' => 'IN',
            ),
        ),
    );
    $template_posts = get_posts($template_args);
?>
    <div class="wrap">
        <?php if ($success) : ?>
            <div class="notice notice-success">
                <p><?php echo esc_html__('Settings saved successfully.', 'direktt-auto-greet'); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('direktt_admin_welcome_save', 'direktt_admin_welcome_nonce'); ?>

            <h2 class="title"><?php echo esc_html__('Welcome Settings', 'direktt-auto-greet'); ?></h2>
			<table class="form-table">
                <tr>
                    <th scope="row"><label for="direktt_welcome_user"><?php echo esc_html__('New Subscribers', 'direktt-auto-greet'); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_welcome_user" id="direktt_welcome_user" value="yes" <?php checked($welcome_user); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_user_template"><?php echo esc_html__('Subscriber Message Template', 'direktt-auto-greet'); ?></label></th>
                    <td>
                        <select name="direktt_welcome_user_template" id="direktt_welcome_user_template">
                            <option value="0"><?php echo esc_html__('Select Template', 'direktt-auto-greet'); ?></option>
                            <?php foreach ($template_posts as $post) : ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($welcome_user_template, $post->ID); ?>>
                                    <?php echo esc_html($post->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__('In message template you can use', 'direktt-auto-greet'); ?> <?php echo esc_html('#title#'); ?> <?php echo esc_html__('placeholder for user name.', 'direktt-auto-greet'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_admin"><?php echo esc_html__('Admin', 'direktt-auto-greet'); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_welcome_admin" id="direktt_welcome_admin" value="yes" <?php checked($welcome_admin); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_admin_template"><?php echo esc_html__('Admin Message Template', 'direktt-auto-greet'); ?></label></th>
                    <td>
                        <select name="direktt_welcome_admin_template" id="direktt_welcome_admin_template">
                            <option value="0"><?php echo esc_html__('Select Template', 'direktt-auto-greet'); ?></option>
                            <?php foreach ($template_posts as $post) : ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($welcome_admin_template, $post->ID); ?>>
                                    <?php echo esc_html($post->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__('In message template you can use', 'direktt-auto-greet'); ?> <?php echo esc_html('#title#'); ?> <?php echo esc_html__('placeholder for user name.', 'direktt-auto-greet'); ?></p>
                        <p class="description"><?php echo esc_html__('and', 'direktt-auto-greet'); ?> <?php echo esc_html('#subscriptionId#'); ?> <?php echo esc_html__('placeholder for Subscription ID.', 'direktt-auto-greet'); ?></p>
                    </td>
                </tr>
			</table>
			 <h2 class="title"><?php echo esc_html__('Out of Office Settings', 'direktt-auto-greet'); ?></h2>
			<table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html__('Out of Office Auto Responder', 'direktt-auto-greet'); ?></th>
                    <td>
                        <select name="direktt_auto_greet_mode" id="direktt_auto_greet_mode">
                            <option value="always" <?php selected($ooo_mode, 'always'); ?>><?php echo esc_html__('Always On', 'direktt-auto-greet'); ?></option>
                            <option value="non-working-hours" <?php selected($ooo_mode, 'non-working-hours'); ?>><?php echo esc_html__('Only During Non-working Hours', 'direktt-auto-greet'); ?></option>
                            <option value="off" <?php selected($ooo_mode, 'off'); ?>><?php echo esc_html__('Off', 'direktt-auto-greet'); ?></option>
                        </select>
                        <p class="description"><?php echo esc_html__('Set the Out of Office auto responder mode.', 'direktt-auto-greet'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Working Hours for Non-working Hours mode', 'direktt-auto-greet'); ?></th>
                    <td>
                        <?php foreach ($ooo_working_hours as $day => $hours) : ?>
                            <div class="direktt-greet-days-row">
                                <div class="direktt-greet-day"><?php echo esc_html(ucfirst($day)); ?></div>
                                <div class="direktt-greet-closed">
                                    <label for="<?php echo esc_attr($day); ?>_closed"><?php echo esc_html__('Closed', 'direktt-auto-greet'); ?></label>
                                    <input type="checkbox" name="<?php echo esc_attr($day); ?>_closed" id="<?php echo esc_attr($day); ?>_closed" value="yes" <?php checked($hours['closed'], true); ?> />
                                </div>
                                <div class="direktt-greet-times">
                                    <input type="time" name="<?php echo esc_attr($day); ?>_start" id="<?php echo esc_attr($day); ?>_start" class="direktt-greet-time-start" value="<?php echo esc_attr($hours['start']); ?>" />
                                    <input type="time" name="<?php echo esc_attr($day); ?>_end" id="<?php echo esc_attr($day); ?>_end" class="direktt-greet-time-end" value="<?php echo esc_attr($hours['end']); ?>" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <p class="description"><?php echo esc_html__('Define working hours for each day. If a day is marked as closed, the auto responder will be active all day.', 'direktt-auto-greet'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Always on mode message template', 'direktt-auto-greet'); ?></th>
                    <td>
                        <select name="direktt_auto_greet_always_template" id="direktt_auto_greet_always_template">
                            <option value="0"><?php echo esc_html__('Select Template', 'direktt-auto-greet'); ?></option>
                            <?php foreach ($template_posts as $post) : ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($ooo_always_template, $post->ID); ?>>
                                    <?php echo esc_html($post->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Non-working hours mode message template', 'direktt-auto-greet'); ?></th>
                    <td>
                        <select name="direktt_auto_greet_non_working_template" id="direktt_auto_greet_non_working_template">
                            <option value="0"><?php echo esc_html__('Select Template', 'direktt-auto-greet'); ?></option>
                            <?php foreach ($template_posts as $post) : ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($ooo_non_working_template, $post->ID); ?>>
                                    <?php echo esc_html($post->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(esc_html__('Save Settings', 'direktt-auto-greet')); ?>
        </form>
    </div>
<?php
}

add_action('direktt/user/subscribe', 'direktt_auto_greet_on_direktt_subscribe_user');

function direktt_auto_greet_on_direktt_subscribe_user($direktt_user_id)
{
    $user_obj = Direktt_User::get_user_by_subscription_id($direktt_user_id);

    $user_title = get_the_title($user_obj['ID']);

    $welcome_user           = get_option('direktt_welcome_user', 'no') === 'yes';
    $welcome_user_template  = intval(get_option('direktt_welcome_user_template', 0));
    $welcome_admin          = get_option('direktt_welcome_admin', 'no') === 'yes';
    $welcome_admin_template = intval(get_option('direktt_welcome_admin_template', 0));

    if ($welcome_user && $welcome_user_template !== 0) {

        Direktt_Message::send_message_template(
            array($direktt_user_id),
            $welcome_user_template,
            array(
                'title' => $user_title,
            )
        );
    }

    if ($welcome_admin && $welcome_admin_template !== 0) {

        Direktt_Message::send_message_template_to_admin(
            $welcome_admin_template,
            array(
                'title'          => $user_title,
                'subscriptionId' => strval($direktt_user_id),
            )
        );
    }
}

add_action('direktt/event/chat/message_sent', 'direktt_auto_greet_out_off_office_message_sent');

function direktt_auto_greet_out_off_office_message_sent($event)
{
    $subscription_id = $event['direktt_user_id'];

    $ooo_mode                 = get_option('direktt_auto_greet_mode', 'off');
    $ooo_always_template      = intval(get_option('direktt_auto_greet_always_template', 0));
    $ooo_non_working_template = intval(get_option('direktt_auto_greet_non_working_template', 0));
    $ooo_working_hours        = get_option('direktt_auto_greet_working_hours', array());

    if ($ooo_mode === 'off' || ($ooo_mode === 'non-working-hours' && ! isset($ooo_working_hours))) {
        return;
    }

    if ($ooo_mode === 'always') {
        Direktt_Message::send_message_template(
            array($subscription_id),
            $ooo_always_template,
            array()
        );
        return;
    }

    if ($ooo_mode === 'non-working-hours') {
        $current_time = current_time('H:i');
        $current_day  = strtolower(gmdate('l', current_time('timestamp')));

        $is_non_working_time = false;

        $day_info = $ooo_working_hours[$current_day];
        if ($day_info['closed']) {
            $is_non_working_time = true;
        } elseif ($current_time < $day_info['start'] || $current_time > $day_info['end']) {
            $is_non_working_time = true;
        }

        if ($is_non_working_time) {
            Direktt_Message::send_message_template(
                array($subscription_id),
                $ooo_non_working_template,
                array()
            );
        }
    }
}

add_shortcode('direktt_auto_greet', 'direktt_auto_greet_out_of_office_auto_responder_shortcode');

function direktt_auto_greet_out_of_office_auto_responder_shortcode()
{
    if (! Direktt_User::is_direktt_admin()) {
        return;
    }

    // Load stored values
    $ooo_mode = get_option('direktt_auto_greet_mode', 'off');

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['direktt_auto_greet_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['direktt_auto_greet_nonce'])), 'direktt_auto_greet_save')) {
        if (isset($_POST['save'])) {
            update_option('direktt_auto_greet_mode', isset($_POST['direktt_auto_greet_mode']) ? sanitize_text_field(wp_unslash($_POST['direktt_auto_greet_mode'])) : 'off');
            if (isset( $_SERVER['REQUEST_URI'])) {
                $new_url = add_query_arg(array('success_flag' => '1'), esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])));
            } else {
                $new_url = home_url();
            }
            wp_safe_redirect($new_url);
            exit;
        }
    }

    ob_start();
    echo '<div id="direktt-profile-wrapper">';
    echo '<div id="direktt-profile">';
    echo '<div id="direktt-profile-data" class="direktt-profile-data-auto-greet-tool direktt-service">';

    if (isset($_GET['success_flag']) && $_GET['success_flag'] === '1') {
        echo '<div class="notice"><p>' . esc_html__('Settings saved successfully.', 'direktt-auto-greet') . '</p></div>';
    }
?>
    <form method="post" action="">
        <?php wp_nonce_field('direktt_auto_greet_save', 'direktt_auto_greet_nonce'); ?>
        <h3><?php echo esc_html__('Out of Office Auto Responder', 'direktt-auto-greet'); ?></h3>
        <p>
            <label for="direktt_auto_greet_mode"><?php echo esc_html__('Select Mode:', 'direktt-auto-greet'); ?></label>
            <select name="direktt_auto_greet_mode" id="direktt_auto_greet_mode">
                <option value="always" <?php selected($ooo_mode, 'always'); ?>><?php echo esc_html__('Always On', 'direktt-auto-greet'); ?></option>
                <option value="non-working-hours" <?php selected($ooo_mode, 'non-working-hours'); ?>><?php echo esc_html__('Only During Non-working Hours', 'direktt-auto-greet'); ?></option>
                <option value="off" <?php selected($ooo_mode, 'off'); ?>><?php echo esc_html__('Off', 'direktt-auto-greet'); ?></option>
            </select>
        </p>
        <button type="submit" name="save" class="direktt-button button-primary button-large"><?php echo esc_html__('Save Settings', 'direktt-auto-greet'); ?></button>
    </form>
<?php
    echo '</div>';
    echo '</div>';
    echo '</div>';
    return ob_get_clean();
}
