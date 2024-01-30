<?php

/**
 * Plugin Name: Discount ESP Influencer Referral Program Signup Form
 * Description: A form that allows users to sign up for the Discount ESP Influencer Referral Program
 * Version: 1.0
 * Author: Aiden Merrill
 */

function member_form_plugin_enqueue_scripts()
{
    $script_url = (defined('WP_DEBUG') && WP_DEBUG) ? 'http://localhost:3000/src/main.js' : plugin_dir_url(__FILE__) . 'dist/assets/main-Dj2qcpKZ.js';
    wp_enqueue_script('plugin-script', $script_url, array(), false, true);
}

function member_form_plugin_enqueue_styles()
{
    // Assuming Vite generated a single CSS file
    $style_url = plugin_dir_url(__FILE__) . 'dist/assets/main-0Dd-jpNm.css';
    wp_enqueue_style('plugin-styles', $style_url);
}

add_action('wp_enqueue_scripts', 'member_form_plugin_enqueue_styles');
add_action('wp_enqueue_scripts', 'member_form_plugin_enqueue_scripts');

function member_form_plugin_shortcode()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'public/index.php';
    return ob_get_clean();
}

add_shortcode('member_signup_form', 'member_form_plugin_shortcode');
