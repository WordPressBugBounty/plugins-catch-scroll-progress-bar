<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

/*
 * Note on ctp_ prefix: these functions are part of a shared library used
 * across all Catch Plugins. The ctp_ prefix (Catch Themes Plugin) is
 * intentional and consistent across the entire Catch plugin family.
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

if (! function_exists('ctp_register_settings')) {
	function ctp_register_settings()
	{
		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting(
			'ctp-group',
			'ctp_options',
			'ctp_sanitize_options'
		);
	}
}
add_action('admin_init', 'ctp_register_settings');

if (! function_exists('ctp_sanitize_options')) {
	/**
	 * Sanitize ctp_options before saving.
	 *
	 * @since  2.2
	 * @param  array $input Raw input array.
	 * @return array Sanitized options.
	 */
	function ctp_sanitize_options($input)
	{
		$output = ctp_default_options();
		if (isset($input['theme_plugin_tabs'])) {
			$output['theme_plugin_tabs'] = (int) $input['theme_plugin_tabs'] ? 1 : 0;
		}
		return $output;
	}
}

if (! function_exists('ctp_get_options')) {
	/**
	 * Returns the options array for ctp_get options
	 *
	 *  @since    1.9
	 */
	function ctp_get_options()
	{
		$defaults = ctp_default_options();
		$options  = get_option('ctp_options', $defaults);

		return wp_parse_args($options, $defaults);
	}
}

if (! function_exists('ctp_default_options')) {
	/**
	 * Return array of default options
	 *
	 * @since     1.9
	 * @return    string    1 or 2.
	 */
	function ctp_default_options($option = null)
	{
		$default_options['theme_plugin_tabs'] = 1;
		if (null == $option) {
			return apply_filters('ctp_options', $default_options); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		} else {
			return $default_options[$option];
		}
	}
}

if (! function_exists('ctp_switch')) {
	/**
	 * AJAX handler: toggle a ctp_options value.
	 *
	 * @since     1.2
	 */
	function ctp_switch()
	{
		// Verify nonce before making any changes.
		if (! check_ajax_referer('ctp_tabs_nonce', 'security', false)) {
			wp_die(esc_html__('Invalid Nonce', 'catch-scroll-progress-bar'));
		}

		if (! current_user_can('manage_options')) {
			wp_die(esc_html__('Permission denied!', 'catch-scroll-progress-bar'));
		}

		// Unslash and sanitize both POST values.
		$value       = ( isset( $_POST['value'] ) && 'true' === wp_unslash( $_POST['value'] ) ) ? 1 : 0; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$option_name = isset($_POST['option_name']) ? sanitize_key(wp_unslash($_POST['option_name'])) : '';

		if (empty($option_name)) {
			wp_die(esc_html__('Invalid option name.', 'catch-scroll-progress-bar'));
		}

		$option_value                = ctp_get_options();
		$option_value[$option_name]  = $value;

		if (update_option('ctp_options', $option_value)) {
			echo esc_html((string) $value);
		} else {
			esc_html_e('Connection Error. Please try again.', 'catch-scroll-progress-bar');
		}

		wp_die(); // Required to terminate immediately and return a proper response.
	}
}
add_action('wp_ajax_ctp_switch', 'ctp_switch');

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
