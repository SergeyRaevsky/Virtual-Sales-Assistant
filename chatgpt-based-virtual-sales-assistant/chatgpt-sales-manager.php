<?php
/*
Plugin Name: ChatGPT Sales Manager
Description: A sales manager chatbot plugin using ChatGPT.
Version: 1.0
Author: Your Name
*/

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'tpl/frontend-functions.php';
include_once plugin_dir_path(__FILE__) . 'admin/admin-functions.php';
include_once plugin_dir_path(__FILE__) . 'tpl/ajax-functions.php';

// Enqueue scripts and styles
function chatgpt_sales_manager_enqueue_scripts() {
    wp_enqueue_style('dashicons'); // Enqueue Dashicons
    wp_enqueue_style('chatgpt_sales_manager_css', plugin_dir_url(__FILE__) . 'css/frontend-style.css');
    wp_enqueue_script('chatgpt_sales_manager_js', plugin_dir_url(__FILE__) . 'js/frontend-script.js', array('jquery'), null, true);

    // Localize script for AJAX
    $business_info = get_option('chatgpt_sales_manager_business_info', __('We specialize in creating high-quality websites and online stores. Our working hours are Monday to Friday from 9:00 AM to 6:00 PM, and Saturday from 10:00 AM to 4:00 PM. Sunday is a day off.', 'chatgpt-sales-manager'));
    $manager_name = get_option('chatgpt_sales_manager_virtual_manager_name', __('Manager', 'chatgpt-sales-manager'));
    $greeting = get_option('chatgpt_sales_manager_virtual_manager_greeting', __('Hello! How can I help you today? Please tell me your name.', 'chatgpt-sales-manager'));
    $chat_title = get_option('chatgpt_sales_manager_chat_title', __('Chat', 'chatgpt-sales-manager'));
    $avatar_url = get_option('chatgpt_sales_manager_virtual_manager_avatar', '');

    wp_localize_script('chatgpt_sales_manager_js', 'chatgpt_sales_manager', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('chatgpt_sales_manager_nonce'),
        'business_info' => $business_info,
        'manager_name' => $manager_name,
        'greeting' => $greeting,
        'chat_title' => $chat_title,
        'avatar_url' => $avatar_url,
        'you_label' => __('You', 'chatgpt-sales-manager'),
        'send_button_text' => __('Send', 'chatgpt-sales-manager'),
        'error_message' => __('Error: ', 'chatgpt-sales-manager'),
        'generic_error_message' => __('An error occurred while processing your request.', 'chatgpt-sales-manager'),
        'bot_avatar_alt' => __('Bot Avatar', 'chatgpt-sales-manager'),
        'default_user_name' => __('User', 'chatgpt-sales-manager')
    ));
}
add_action('wp_enqueue_scripts', 'chatgpt_sales_manager_enqueue_scripts');

// Ensure the chat interface is added to the page
add_action('wp_footer', 'chatgpt_sales_manager_add_chat_interface');

// Hook for creating database table on activation
register_activation_hook(__FILE__, 'chatgpt_sales_manager_create_db');

// Function to create database table
function chatgpt_sales_manager_create_db() {
    global $wpdb;
    $table_name_messages = $wpdb->prefix . 'chatgpt_sales_manager_messages';
    $table_name_dialogs = $wpdb->prefix . 'chatgpt_sales_manager_dialogs';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql_messages = "CREATE TABLE $table_name_messages (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        dialog_id mediumint(9) NOT NULL,
        user_name varchar(255) NOT NULL,
        message text NOT NULL,
        message_type varchar(50) NOT NULL,
        time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (dialog_id) REFERENCES $table_name_dialogs(id) ON DELETE CASCADE
    ) $charset_collate;";

    $sql_dialogs = "CREATE TABLE $table_name_dialogs (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_name varchar(255) NOT NULL,
        start_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_dialogs);
    dbDelta($sql_messages);

    // Logging for debugging
    if($wpdb->last_error) {
        error_log("Database error: " . $wpdb->last_error);
    } else {
        error_log("Tables $table_name_messages and $table_name_dialogs created successfully.");
    }
}
?>
