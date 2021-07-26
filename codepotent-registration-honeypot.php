<?php

/**
 * -----------------------------------------------------------------------------
 * Plugin Name: Registration Honeypot
 * Description: Add a honeypot input to the ClassicPress registration form to prevent spambots from creating accounts.
 * Version: 1.1.1
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

// Include constants file.
require_once(plugin_dir_path(__FILE__).'includes/constants.php');

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
