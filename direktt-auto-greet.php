<?php

/**
 * Plugin Name: Direktt Auto Greet - Welcome + Out of Office Automated Messages
 * Description: Direktt Customer Review Direktt Plugin
 * Version: 1.0.0
 * Author: Direktt
 * Author URI: https://direktt.com/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded', 'direktt_auto_greet_activation_check', -20 );

function direktt_auto_greet_activation_check() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $required_plugin = 'direktt-plugin/direktt.php';

    if ( ! is_plugin_active( $required_plugin ) ) {
        add_action(
            'after_plugin_row_direktt-auto-greet/direktt-auto-greet.php',
            function ( $plugin_file, $plugin_data, $status ) {
				$colspan = 3;
				?>
            <tr class="plugin-update-tr">
                <td colspan="<?php echo esc_attr( $colspan ); ?>" style="box-shadow: none;">
                    <div style="color: #b32d2e; font-weight: bold;">
                        <?php echo esc_html__( 'Direktt Auto Greet requires the Direktt WordPress Plugin to be active. Please activate Direktt WordPress Plugin first.', 'direktt-auto-greet' ); ?>
                    </div>
                </td>
            </tr>
				<?php
			},
            10,
            3
        );

        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

add_action( 'direktt_setup_settings_pages', 'setup_settings_pages' );

function setup_settings_pages() {

    Direktt::add_settings_page(
        array(
            'id'       => 'welcome-message',
            'label'    => esc_html__( 'Auto Greet Settings', 'direktt-auto-greet' ),
            'callback' => 'render_welcome_settings',
            'priority' => 1,
        )
    );
}

function render_welcome_settings() {
    // Success message flag
    $success = false;

    // Handle form submission
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['direktt_admin_welcome_nonce'] )
        && wp_verify_nonce( $_POST['direktt_admin_welcome_nonce'], 'direktt_admin_welcome_save' )
    ) {
        // Sanitize and update options
        update_option( 'direktt_welcome_user', isset( $_POST['direktt_welcome_user'] ) ? 'yes' : 'no' );
        update_option( 'direktt_welcome_user_template', intval( $_POST['direktt_welcome_user_template'] ) );
        update_option( 'direktt_welcome_admin', isset( $_POST['direktt_welcome_admin'] ) ? 'yes' : 'no' );
        update_option( 'direktt_welcome_admin_template', intval( $_POST['direktt_welcome_admin_template'] ) );
        update_option( 'direktt_auto_greet_mode', sanitize_text_field( $_POST['direktt_auto_greet_mode'] ) );
        update_option( 'direktt_auto_greet_always_template', intval( $_POST['direktt_auto_greet_always_template'] ) );
        update_option( 'direktt_auto_greet_non_working_template', intval( $_POST['direktt_auto_greet_non_working_template'] ) );
        update_option(
            'direktt_auto_greet_working_hours',
            array(
				'monday'    => array(
					'closed' => isset( $_POST['monday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['monday_start'] ),
					'end'    => sanitize_text_field( $_POST['monday_end'] ),
				),
				'tuesday'   => array(
					'closed' => isset( $_POST['tuesday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['tuesday_start'] ),
					'end'    => sanitize_text_field( $_POST['tuesday_end'] ),
				),
				'wednesday' => array(
					'closed' => isset( $_POST['wednesday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['wednesday_start'] ),
					'end'    => sanitize_text_field( $_POST['wednesday_end'] ),
				),
				'thursday'  => array(
					'closed' => isset( $_POST['thursday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['thursday_start'] ),
					'end'    => sanitize_text_field( $_POST['thursday_end'] ),
				),
				'friday'    => array(
					'closed' => isset( $_POST['friday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['friday_start'] ),
					'end'    => sanitize_text_field( $_POST['friday_end'] ),
				),
				'saturday'  => array(
					'closed' => isset( $_POST['saturday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['saturday_start'] ),
					'end'    => sanitize_text_field( $_POST['saturday_end'] ),
				),
				'sunday'    => array(
					'closed' => isset( $_POST['sunday_closed'] ) ? true : false,
					'start'  => sanitize_text_field( $_POST['sunday_start'] ),
					'end'    => sanitize_text_field( $_POST['sunday_end'] ),
				),
			)
        );
        $success = true;
    }

    // Load stored values
    $welcome_user             = get_option( 'direktt_welcome_user', 'no' ) === 'yes';
    $welcome_user_template    = intval( get_option( 'direktt_welcome_user_template', 0 ) );
    $welcome_admin            = get_option( 'direktt_welcome_admin', 'no' ) === 'yes';
    $welcome_admin_template   = intval( get_option( 'direktt_welcome_admin_template', 0 ) );
    $ooo_mode                 = get_option( 'direktt_auto_greet_mode', 'off' );
    $ooo_always_template      = intval( get_option( 'direktt_auto_greet_always_template', 0 ) );
    $ooo_non_working_template = intval( get_option( 'direktt_auto_greet_non_working_template', 0 ) );
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
        'meta_query'     => array(
            array(
                'key'     => 'direkttMTType',
                'value'   => array( 'all', 'none' ),
                'compare' => 'IN',
            ),
        ),
    );
    $template_posts = get_posts( $template_args );
	?>
    <div class="wrap">
        <?php if ( $success ) : ?>
            <div class="updated notice is-dismissible">
                <p><?php echo esc_html__( 'Settings saved successfully.', 'direktt-auto-greet' ); ?></p>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'direktt_admin_welcome_save', 'direktt_admin_welcome_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th scope="row" style="padding: 0px;"><h2 style="margin: 0px;"><?php echo esc_html__( 'Welcome Settings', 'direktt-auto-greet' ); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_user"><?php echo esc_html__( 'New Subscribers', 'direktt-auto-greet' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_welcome_user" id="direktt_welcome_user" value="yes" <?php checked( $welcome_user ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_user_template"><?php echo esc_html__( 'Subscriber Message Template', 'direktt-auto-greet' ); ?></label></th>
                    <td>
                        <select name="direktt_welcome_user_template" id="direktt_welcome_user_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-auto-greet' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $welcome_user_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-auto-greet' ); ?> <?php echo esc_html( '#title#' ); ?> <?php echo esc_html__( 'placeholder for user name.', 'direktt-auto-greet' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_admin"><?php echo esc_html__( 'Admin', 'direktt-auto-greet' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_welcome_admin" id="direktt_welcome_admin" value="yes" <?php checked( $welcome_admin ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_welcome_admin_template"><?php echo esc_html__( 'Admin Message Template', 'direktt-auto-greet' ); ?></label></th>
                    <td>
                        <select name="direktt_welcome_admin_template" id="direktt_welcome_admin_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-auto-greet' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $welcome_admin_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-auto-greet' ); ?> <?php echo esc_html( '#title#' ); ?> <?php echo esc_html__( 'placeholder for user name.', 'direktt-auto-greet' ); ?></p>
                        <p class="description"><?php echo esc_html__( 'and', 'direktt-auto-greet' ); ?> <?php echo esc_html( '#subscriptionId#' ); ?> <?php echo esc_html__( 'placeholder for Subscription ID.', 'direktt-auto-greet' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row" style="padding: 0px;"><h2 style="margin: 0px;"><?php echo esc_html__( 'Out of Office Settings', 'direktt-auto-greet' ); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__( 'Out of Office Auto Responder', 'direktt-auto-greet' ); ?></th>
                    <td>
                        <select name="direktt_auto_greet_mode" id="direktt_auto_greet_mode">
                            <option value="always" <?php selected( $ooo_mode, 'always' ); ?>><?php echo esc_html__( 'Always On', 'direktt-auto-greet' ); ?></option>
                            <option value="non-working-hours" <?php selected( $ooo_mode, 'non-working-hours' ); ?>><?php echo esc_html__( 'Only During Non-working Hours', 'direktt-auto-greet' ); ?></option>
                            <option value="off" <?php selected( $ooo_mode, 'off' ); ?>><?php echo esc_html__( 'Off', 'direktt-auto-greet' ); ?></option>
                        </select>
                        <p class="description"><?php echo esc_html__( 'Set the Out of Office auto responder mode.', 'direktt-auto-greet' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__( 'Working Hours for Non-working Hours mode', 'direktt-auto-greet' ); ?></th>
                    <td>
                        <?php foreach ( $ooo_working_hours as $day => $hours ) : ?>
                            <div style="margin-top: 10px;">
                                <label for="<?php echo esc_attr( $day ); ?>_closed"><?php echo esc_html( ucfirst( $day ) ); ?> <?php echo esc_html__( 'Closed', 'direktt-auto-greet' ); ?></label>
                                <input type="checkbox" name="<?php echo esc_attr( $day ); ?>_closed" id="<?php echo esc_attr( $day ); ?>_closed" value="yes" <?php checked( $hours['closed'], true ); ?> />
                                <input type="time" name="<?php echo esc_attr( $day ); ?>_start" id="<?php echo esc_attr( $day ); ?>_start" value="<?php echo esc_attr( $hours['start'] ); ?>" />
                                <input type="time" name="<?php echo esc_attr( $day ); ?>_end" id="<?php echo esc_attr( $day ); ?>_end" value="<?php echo esc_attr( $hours['end'] ); ?>" />
                            </div>
                        <?php endforeach; ?>
                        <p class="description"><?php echo esc_html__( 'Define working hours for each day. If a day is marked as closed, the auto responder will be active all day.', 'direktt-auto-greet' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__( 'Always on mode message template', 'direktt-auto-greet' ); ?></th>
                    <td>
                        <select name="direktt_auto_greet_always_template" id="direktt_auto_greet_always_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-auto-greet' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $ooo_always_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__( 'Non-working hours mode message template', 'direktt-auto-greet' ); ?></th>
                    <td>
                        <select name="direktt_auto_greet_non_working_template" id="direktt_auto_greet_non_working_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-auto-greet' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $ooo_non_working_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button( esc_html__( 'Save Settings', 'direktt-auto-greet' ) ); ?>
        </form>
    </div>
	<?php
}

add_action( 'direktt/user/subscribe', 'on_direktt_subscribe_user' );

function on_direktt_subscribe_user( $direktt_user_id ) {
    $user_obj = Direktt_User::get_user_by_subscription_id( $direktt_user_id );

    $user_title = get_the_title( $user_obj['ID'] );

    $welcome_user           = get_option( 'direktt_welcome_user', 'no' ) === 'yes';
    $welcome_user_template  = intval( get_option( 'direktt_welcome_user_template', 0 ) );
    $welcome_admin          = get_option( 'direktt_welcome_admin', 'no' ) === 'yes';
    $welcome_admin_template = intval( get_option( 'direktt_welcome_admin_template', 0 ) );

    if ( $welcome_user && $welcome_user_template !== 0 ) {

        Direktt_Message::send_message_template(
            array( $direktt_user_id ),
            $welcome_user_template,
            array(
                'title' => $user_title,
            )
        );
    }

    if ( $welcome_admin && $welcome_admin_template !== 0 ) {

        Direktt_Message::send_message_template_to_admin(
            $welcome_admin_template,
            array(
                'title'          => $user_title,
                'subscriptionId' => strval( $direktt_user_id ),
            )
        );
    }
}

add_action( 'direktt/event/chat/message_sent', 'out_off_office_message_sent' );

function out_off_office_message_sent( $event ) {
    $subscription_id = $event['direktt_user_id'];

    $ooo_mode                 = get_option( 'direktt_auto_greet_mode', 'off' );
    $ooo_always_template      = intval( get_option( 'direktt_auto_greet_always_template', 0 ) );
    $ooo_non_working_template = intval( get_option( 'direktt_auto_greet_non_working_template', 0 ) );
    $ooo_working_hours        = get_option( 'direktt_auto_greet_working_hours', array() );

    if ( $ooo_mode === 'off' || ( $ooo_mode === 'non-working-hours' && ! isset( $ooo_working_hours ) ) ) {
        return;
    }

    if ( $ooo_mode === 'always' ) {
        Direktt_Message::send_message_template(
            array( $subscription_id ),
            $ooo_always_template,
            array()
        );
        return;
    }

    if ( $ooo_mode === 'non-working-hours' ) {
        $current_time = current_time( 'H:i' );
        $current_day  = strtolower( date( 'l', current_time( 'timestamp' ) ) );

        $is_non_working_time = false;

        $day_info = $ooo_working_hours[ $current_day ];
        if ( $day_info['closed'] ) {
            $is_non_working_time = true;
        } elseif ( $current_time < $day_info['start'] || $current_time > $day_info['end'] ) {
                $is_non_working_time = true;
        }

        if ( $is_non_working_time ) {
            Direktt_Message::send_message_template(
                array( $subscription_id ),
                $ooo_non_working_template,
                array()
            );
        }
    }
}

add_shortcode( 'direktt_auto_greet', 'out_of_office_auto_responder_shortcode' );

function out_of_office_auto_responder_shortcode() {
    if ( ! Direktt_User::is_direktt_admin() ) {
        return;
    }

    // Load stored values
    $ooo_mode = get_option( 'direktt_auto_greet_mode', 'off' );

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['direktt_auto_greet_nonce'] ) && wp_verify_nonce( $_POST['direktt_auto_greet_nonce'], 'direktt_auto_greet_save' ) ) {
        if ( isset( $_POST['save'] ) ) {
            update_option( 'direktt_auto_greet_mode', sanitize_text_field( $_POST['direktt_auto_greet_mode'] ) );
            set_transient( 'direktt_auto_greet_success_message', 'Settings saved successfully.', 30 );
            wp_safe_redirect( $_SERVER['REQUEST_URI'] );
            exit;
        }
    }

    ob_start();
    echo '<div class="direktt-profile-wrapper">';
    if ( $message = get_transient( 'direktt_auto_greet_success_message' ) ) {
        echo '<div class="updated notice is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
        delete_transient( 'direktt_auto_greet_success_message' ); // Clear the message after it's shown
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'direktt_auto_greet_save', 'direktt_auto_greet_nonce' ); ?>
        <h2><?php echo esc_html__( 'Out of Office Auto Responder', 'direktt-auto-greet' ); ?></h2>
        <div style="margin-bottom: 20px;">
            <label for="direktt_auto_greet_mode"><?php echo esc_html__( 'Select Mode:', 'direktt-auto-greet' ); ?></label>
            <select name="direktt_auto_greet_mode" id="direktt_auto_greet_mode">
                <option value="always" <?php selected( $ooo_mode, 'always' ); ?>><?php echo esc_html__( 'Always On', 'direktt-auto-greet' ); ?></option>
                <option value="non-working-hours" <?php selected( $ooo_mode, 'non-working-hours' ); ?>><?php echo esc_html__( 'Only During Non-working Hours', 'direktt-auto-greet' ); ?></option>
                <option value="off" <?php selected( $ooo_mode, 'off' ); ?>><?php echo esc_html__( 'Off', 'direktt-auto-greet' ); ?></option>
            </select>
        </div>

        <button type="submit" name="save" class="button button-primary"><?php echo esc_html__( 'Save Settings', 'direktt-auto-greet' ); ?></button>
    </form>
    <?php
    return ob_get_clean();
}
