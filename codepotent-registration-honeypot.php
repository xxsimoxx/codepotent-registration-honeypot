<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: Registration Honeypot
 * Description: Add a honeypot input to the ClassicPress registration form to prevent spambots from creating accounts.
 * Version: 1.1.0
 * Author: Simone Fioravanti
 * Author URI: https://software.gieffeedizioni.it
 * Plugin URI: https://software.gieffeedizioni.it
 * Text Domain: codepotent-registration-honeypot
 * Domain Path: /languages
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 * Copyright 2021, John Alarcon (Code Potent)
 * -----------------------------------------------------------------------------
 * Adopted by Simone Fioravanti, 06/01/2021
 * -----------------------------------------------------------------------------
 */

// Declare the namespace.
namespace CodePotent\RegistrationHoneypot;

// Prevent direct access.
if (!defined('ABSPATH')) {
	die();
}

// Include update client.
require_once(plugin_dir_path(__FILE__).'classes/UpdateClient.class.php');

// Enqueue Javascript.
add_action('login_footer', __NAMESPACE__.'\enqueue_login_script');
function enqueue_login_script() {
	wp_enqueue_script('registration-extra', plugin_dir_url(__FILE__).'scripts/login.js', ['jquery']);
}

// Enqueue CSS.
add_action('login_enqueue_scripts', __NAMESPACE__.'\enqueue_login_style');
function enqueue_login_style() {
	wp_enqueue_style('registration-extra', plugin_dir_url(__FILE__).'styles/login.css');
}

// Add honeypot input.
add_action('register_form', __NAMESPACE__.'\append_honeypot_input');
function append_honeypot_input() {
	echo '<p class="register_additional">';
	echo '<label for="register_additional">'.esc_html__('Leave this field empty.', 'registration-honeypot').'</label><br />';
	echo '<input type="text" name="register_additional" id="register_additional" value="" autocomplete="off" /></label>';
	echo '</p>';
}

// Prevent spambot registrations.
add_action('register_post', __NAMESPACE__.'\check_honeypot_input', 0);
add_action('login_form_register', __NAMESPACE__.'\check_honeypot_input', 0);
function check_honeypot_input() {
	if (empty($_POST['register_additional'])) {
		return;
	}
	wp_die(esc_html__('Automated registration is disabled.', 'registration-honeypot'));
}

// POST-ADOPTION: Remove these actions before pushing your next update.
add_action('upgrader_process_complete', 'codepotent_enable_adoption_notice', 10, 2);
add_action('admin_notices', 'codepotent_display_adoption_notice');

// POST-ADOPTION: Remove this function before pushing your next update.
function codepotent_enable_adoption_notice($upgrader_object, $options) {
	if ($options['action'] === 'update') {
		if ($options['type'] === 'plugin') {
			if (!empty($options['plugins'])) {
				if (in_array(plugin_basename(__FILE__), $options['plugins'])) {
					set_transient(PLUGIN_PREFIX.'_adoption_complete', 1);
				}
			}
		}
	}
}

// POST-ADOPTION: Remove this function before pushing your next update.
function codepotent_display_adoption_notice() {
	if (get_transient(PLUGIN_PREFIX.'_adoption_complete')) {
		delete_transient(PLUGIN_PREFIX.'_adoption_complete');
		echo '<div class="notice notice-success is-dismissible">';
		echo '<h3 style="margin:25px 0 15px;padding:0;color:#e53935;">IMPORTANT <span style="color:#aaa;">information about the <strong style="color:#333;">'.PLUGIN_NAME.'</strong> plugin</h3>';
		echo '<p style="margin:0 0 15px;padding:0;font-size:14px;">The <strong>'.PLUGIN_NAME.'</strong> plugin has been officially adopted and is now managed by <a href="'.PLUGIN_AUTHOR_URL.'" rel="noopener" target="_blank" style="text-decoration:none;">'.PLUGIN_AUTHOR.'<span class="dashicons dashicons-external" style="display:inline;font-size:98%;"></span></a>, a longstanding and trusted ClassicPress developer and community member. While it has been wonderful to serve the ClassicPress community with free plugins, tutorials, and resources for nearly 3 years, it\'s time that I move on to other endeavors. This notice is to inform you of the change, and to assure you that the plugin remains in good hands. I\'d like to extend my heartfelt thanks to you for making my plugins a staple within the community, and wish you great success with ClassicPress!</p>';
		echo '<p style="margin:0 0 15px;padding:0;font-size:14px;font-weight:600;">All the best!</p>';
		echo '<p style="margin:0 0 15px;padding:0;font-size:14px;">~ John Alarcon <span style="color:#aaa;">(Code Potent)</span></p>';
		echo '</div>';
	}
}