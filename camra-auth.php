<?php
/*
Plugin Name: CAMRA Auth
Description: A Wordpress plugin for authenticating CAMRA members.
Author: Tom Blakemore
*/

if (!function_exists('add_action')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'class.camra-auth-response.php');
require_once(plugin_dir_path(__FILE__) . 'class.camra-auth-member.php');
require_once(plugin_dir_path(__FILE__) . 'camra-auth-utilities.php');

register_activation_hook(__FILE__, ['CAMRAAuth_Response', 'activate']);
register_deactivation_hook(__FILE__, ['CAMRAAuth_Response', 'deactivate']);

/**
 * Define an options page for the admin.
 *
 * @return void
 */
function camra_auth_admin_menu()
{
    add_options_page('CAMRA Auth Settings', 'CAMRA Auth', 'manage_options', 'camra_auth', 'camra_auth_output_admin_options');
}

/**
 * Register the session when the plugin initialises.
 *
 * @return void
 */
function camra_auth_init()
{
    if (!session_id()) {
        session_start();
    }
}

/**
 * Check if a member is logged in, and if not redirect them to the login page.
 *
 * @return void
 */
function camra_auth_member_check()
{
    if (camra_auth_is_members_only() && !is_camra_auth_member_logged_in()) {
        $_SESSION['camra_auth_redirect_uri'] = array_get($_SERVER, 'REQUEST_URI');
        wp_redirect(home_url('/login/'));
        exit;
    }
}

/**
 * Check if a post is members only.
 *
 * @param mixed $post
 * @return bool
 */
function camra_auth_is_members_only($post = null)
{
    if (!$post) {
        $post = get_post();
    } else {
        $post = get_post($post);
    }

    if ($post->post_type === 'nav_menu_item') {
        $post = get_post(get_post_meta($post->ID, '_menu_item_object_id', true));
    }

    $ancestors = get_post_ancestors($post);

    if (!empty($ancestors)) {
        $post = get_post(end($ancestors));
    }

    if ($post->post_name === 'members') {
        return true;
    }

    return false;
}

/**
 * Output the HTML for the plugin options page in the admin tool.
 *
 * @return string
 */
function camra_auth_output_admin_options()
{
    if (!current_user_can('manage_options') )  {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if (!($options = get_option('camra_auth'))) {
        wp_die(__('Missing the options.'));
    }

    ob_start();
    include(plugin_dir_path(__FILE__) . 'camra-auth-admin-options.php');
    echo ob_get_clean();
}

/**
 * Ouput the HTML for member login form.
 *
 * @return string
 */
function camra_auth_output_login_form()
{
    $camra_auth_member = new CAMRAAuth_Member(array_get($_POST, 'camra_auth_memno'));

    ob_start();
    include(plugin_dir_path(__FILE__) . 'camra-auth-login-form.php');
    return ob_get_clean();
}

/**
 * Check for post requests once WP and all its dependencies have loaded.
 *
 * @return void
 */
function camra_auth_wp_loaded()
{
    if (is_camra_auth_login_path() && is_camra_auth_member_logged_in()) {
        wp_redirect(home_url('/members/'));
        exit;
    }

    if (is_camra_auth_logout_path()) {
        unset($_SESSION['camra_auth_memno']);
        unset($_SESSION['camra_auth_redirect_uri']);
        wp_redirect(home_url());
        exit;
    }

    if (array_key_exists('login_form', $_POST) && $_POST['login_form'] === 'camra_auth') {

        $camra_auth_member = CAMRAAuth_Member::login(
            array_get($_POST, 'camra_auth_memno'),
            array_get($_POST, 'camra_auth_pass')
        );

        if ($camra_auth_member->authentic()) {

            $_SESSION['camra_auth_memno'] = $camra_auth_member->memno();

            if (($redirect_uri = array_get($_SESSION, 'camra_auth_redirect_uri'))) {
                $location = home_url($redirect_uri);
            } else {
                $location = home_url();
            }

            unset($_SESSION['camra_auth_redirect_uri']);
            wp_redirect($location);
            exit;
        }
    }

    if (array_key_exists('option_page', $_POST) && $_POST['option_page'] === 'camra_auth') {

        update_option('camra_auth', [
            'branch_code' => array_get($_POST, 'camra_auth_branch_code'),
            'key' => array_get($_POST, 'camra_auth_key'),
            'timeout' => intval(array_get($_POST, 'camra_auth_timeout')),
            'url' => array_get($_POST, 'camra_auth_url'),
            'ssl_trust_certs' => array_get($_POST, 'camra_auth_ssl_trust_certs'),
            'ssl_verifypeer' => isset($_POST['camra_auth_ssl_verifypeer'])
        ]);
    }
}

add_action('admin_menu', 'camra_auth_admin_menu');
add_action('init', 'camra_auth_init');
add_action('template_redirect', 'camra_auth_member_check');
add_action('wp_loaded', 'camra_auth_wp_loaded');

add_shortcode('camra_auth_login_form', 'camra_auth_output_login_form');
