<?php
// Add settings page
add_action('admin_menu', 'chatgpt_sales_manager_menu');

function chatgpt_sales_manager_menu() {
    add_menu_page(
        __('ChatGPT Sales Manager', 'chatgpt-sales-manager'),
        __('ChatGPT Manager', 'chatgpt-sales-manager'),
        'manage_options',
        'chatgpt-sales-manager',
        'chatgpt_sales_manager_settings_page',
        'dashicons-format-chat',
        6
    );

    add_submenu_page(
        'chatgpt-sales-manager',
        __('ChatGPT Messages', 'chatgpt-sales-manager'),
        __('Messages', 'chatgpt-sales-manager'),
        'manage_options',
        'chatgpt-sales-manager-messages',
        'chatgpt_sales_manager_messages_page'
    );
}

function chatgpt_sales_manager_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('ChatGPT Sales Manager Settings', 'chatgpt-sales-manager'); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'chatgpt-sales-manager'); ?></a>
            <a href="#appearance" class="nav-tab"><?php _e('Appearance', 'chatgpt-sales-manager'); ?></a>
            <a href="#business-info" class="nav-tab"><?php _e('Business Info', 'chatgpt-sales-manager'); ?></a>
            <a href="#virtual-manager" class="nav-tab"><?php _e('Virtual Manager', 'chatgpt-sales-manager'); ?></a>
        </h2>
        <div id="general" class="tab-content">
            <form method="post" action="options.php">
                <?php
                settings_fields('chatgpt_sales_manager_options_group');
                do_settings_sections('chatgpt-sales-manager');
                submit_button();
                ?>
            </form>
        </div>
        <div id="appearance" class="tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php
                settings_fields('chatgpt_sales_manager_appearance_options_group');
                do_settings_sections('chatgpt-sales-manager-appearance');
                submit_button();
                ?>
            </form>
        </div>
        <div id="business-info" class="tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php
                settings_fields('chatgpt_sales_manager_business_info_group');
                do_settings_sections('chatgpt-sales-manager-business-info');
                submit_button();
                ?>
            </form>
        </div>
        <div id="virtual-manager" class="tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php
                settings_fields('chatgpt_sales_manager_virtual_manager_group');
                do_settings_sections('chatgpt-sales-manager-virtual-manager');
                submit_button();
                ?>
            </form>
        </div>
    </div>
    <?php
}

add_action('admin_init', 'chatgpt_sales_manager_settings_init');

function chatgpt_sales_manager_settings_init() {
    register_setting('chatgpt_sales_manager_options_group', 'chatgpt_sales_manager_api_key', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
    register_setting('chatgpt_sales_manager_options_group', 'chatgpt_sales_manager_model_version', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'gpt-3.5-turbo'));
    register_setting('chatgpt_sales_manager_options_group', 'chatgpt_sales_manager_chat_title', array('sanitize_callback' => 'sanitize_text_field', 'default' => __('Chat', 'chatgpt-sales-manager')));
    register_setting('chatgpt_sales_manager_appearance_options_group', 'chatgpt_sales_manager_button_style', array('default' => array('border_radius' => '50%', 'background_color' => '#0084ff', 'icon_color' => '#ffffff', 'icon_url' => '')));
    register_setting('chatgpt_sales_manager_business_info_group', 'chatgpt_sales_manager_business_info', array('sanitize_callback' => 'sanitize_textarea_field', 'default' => ''));
    register_setting('chatgpt_sales_manager_business_info_group', 'chatgpt_sales_manager_business_logo', array('sanitize_callback' => 'esc_url_raw', 'default' => ''));
    register_setting('chatgpt_sales_manager_virtual_manager_group', 'chatgpt_sales_manager_virtual_manager_name', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
    register_setting('chatgpt_sales_manager_virtual_manager_group', 'chatgpt_sales_manager_virtual_manager_avatar', array('sanitize_callback' => 'esc_url_raw', 'default' => ''));
    register_setting('chatgpt_sales_manager_virtual_manager_group', 'chatgpt_sales_manager_virtual_manager_greeting', array('sanitize_callback' => 'sanitize_textarea_field', 'default' => ''));

    add_settings_section(
        'chatgpt_sales_manager_settings_section',
        '',
        null,
        'chatgpt-sales-manager'
    );

    add_settings_field(
        'chatgpt_sales_manager_api_key',
        __('OpenAI API Key', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_api_key_render',
        'chatgpt-sales-manager',
        'chatgpt_sales_manager_settings_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_model_version',
        __('GPT Model Version', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_model_version_render',
        'chatgpt-sales-manager',
        'chatgpt_sales_manager_settings_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_chat_title',
        __('Chat Heading / Title', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_chat_title_render',
        'chatgpt-sales-manager',
        'chatgpt_sales_manager_settings_section'
    );

    add_settings_section(
        'chatgpt_sales_manager_appearance_settings_section',
        '',
        null,
        'chatgpt-sales-manager-appearance'
    );

    add_settings_field(
        'chatgpt_sales_manager_button_style_border_radius',
        __('Button Border Radius', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_button_style_border_radius_render',
        'chatgpt-sales-manager-appearance',
        'chatgpt_sales_manager_appearance_settings_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_button_style_background_color',
        __('Button Background Color', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_button_style_background_color_render',
        'chatgpt-sales-manager-appearance',
        'chatgpt_sales_manager_appearance_settings_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_button_style_icon_color',
        __('Button Icon Color', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_button_style_icon_color_render',
        'chatgpt-sales-manager-appearance',
        'chatgpt_sales_manager_appearance_settings_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_button_style_icon_url',
        __('Button Icon Image', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_button_style_icon_url_render',
        'chatgpt-sales-manager-appearance',
        'chatgpt_sales_manager_appearance_settings_section'
    );

    add_settings_section(
        'chatgpt_sales_manager_business_info_section',
        '',
        null,
        'chatgpt-sales-manager-business-info'
    );

    add_settings_field(
        'chatgpt_sales_manager_business_info',
        __('Business Info', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_business_info_render',
        'chatgpt-sales-manager-business-info',
        'chatgpt_sales_manager_business_info_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_business_logo',
        __('Business Logo', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_business_logo_render',
        'chatgpt-sales-manager-business-info',
        'chatgpt_sales_manager_business_info_section'
    );

    add_settings_section(
        'chatgpt_sales_manager_virtual_manager_section',
        '',
        null,
        'chatgpt-sales-manager-virtual-manager'
    );

    add_settings_field(
        'chatgpt_sales_manager_virtual_manager_name',
        __('Manager Name', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_virtual_manager_name_render',
        'chatgpt-sales-manager-virtual-manager',
        'chatgpt_sales_manager_virtual_manager_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_virtual_manager_avatar',
        __('Manager Avatar', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_virtual_manager_avatar_render',
        'chatgpt-sales-manager-virtual-manager',
        'chatgpt_sales_manager_virtual_manager_section'
    );

    add_settings_field(
        'chatgpt_sales_manager_virtual_manager_greeting',
        __('Manager Greeting', 'chatgpt-sales-manager'),
        'chatgpt_sales_manager_virtual_manager_greeting_render',
        'chatgpt-sales-manager-virtual-manager',
        'chatgpt_sales_manager_virtual_manager_section'
    );
}

function chatgpt_sales_manager_api_key_render() {
    $apiKey = get_option('chatgpt_sales_manager_api_key', '');
    ?>
    <input type="text" name="chatgpt_sales_manager_api_key" value="<?php echo esc_attr($apiKey); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_model_version_render() {
    $model_version = get_option('chatgpt_sales_manager_model_version', 'gpt-3.5-turbo');
    ?>
    <select name="chatgpt_sales_manager_model_version" style="width: 100%; max-width: 600px;">
        <option value="gpt-3.5-turbo" <?php selected($model_version, 'gpt-3.5-turbo'); ?>><?php _e('GPT-3.5', 'chatgpt-sales-manager'); ?></option>
        <option value="gpt-4" <?php selected($model_version, 'gpt-4'); ?>><?php _e('GPT-4', 'chatgpt-sales-manager'); ?></option>
    </select>
    <?php
}

function chatgpt_sales_manager_chat_title_render() {
    $chat_title = get_option('chatgpt_sales_manager_chat_title', __('Chat', 'chatgpt-sales-manager'));
    ?>
    <input type="text" name="chatgpt_sales_manager_chat_title" value="<?php echo esc_attr($chat_title); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_button_style_border_radius_render() {
    $button_style = get_option('chatgpt_sales_manager_button_style', ['border_radius' => '50%']);
    $border_radius = isset($button_style['border_radius']) ? $button_style['border_radius'] : '50%';
    ?>
    <input type="text" name="chatgpt_sales_manager_button_style[border_radius]" value="<?php echo esc_attr($border_radius); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_button_style_background_color_render() {
    $button_style = get_option('chatgpt_sales_manager_button_style', ['background_color' => '#0084ff']);
    $background_color = isset($button_style['background_color']) ? $button_style['background_color'] : '#0084ff';
    ?>
    <input type="text" class="color-field" name="chatgpt_sales_manager_button_style[background_color]" value="<?php echo esc_attr($background_color); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_button_style_icon_color_render() {
    $button_style = get_option('chatgpt_sales_manager_button_style', ['icon_color' => '#ffffff']);
    $icon_color = isset($button_style['icon_color']) ? $button_style['icon_color'] : '#ffffff';
    ?>
    <input type="text" class="color-field" name="chatgpt_sales_manager_button_style[icon_color]" value="<?php echo esc_attr($icon_color); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_button_style_icon_url_render() {
    $button_style = get_option('chatgpt_sales_manager_button_style', ['icon_url' => '']);
    $icon_url = isset($button_style['icon_url']) ? $button_style['icon_url'] : '';
    ?>
    <input type="text" name="chatgpt_sales_manager_button_style[icon_url]" value="<?php echo esc_attr($icon_url); ?>" id="chatgpt_sales_manager_button_style_icon_url" style="width: 100%; max-width: 600px;">
    <button type="button" class="button" id="upload_image_button"><?php _e('Upload Image', 'chatgpt-sales-manager'); ?></button>
    <?php
}

function chatgpt_sales_manager_business_info_render() {
    $business_info = get_option('chatgpt_sales_manager_business_info', '');
    ?>
    <textarea name="chatgpt_sales_manager_business_info" rows="5" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($business_info); ?></textarea>
    <?php
}

function chatgpt_sales_manager_business_logo_render() {
    $business_logo = get_option('chatgpt_sales_manager_business_logo', '');
    ?>
    <input type="text" name="chatgpt_sales_manager_business_logo" value="<?php echo esc_attr($business_logo); ?>" id="chatgpt_sales_manager_business_logo" style="width: 100%; max-width: 600px;">
    <button type="button" class="button" id="upload_logo_button"><?php _e('Upload Logo', 'chatgpt-sales-manager'); ?></button>
    <?php
}

function chatgpt_sales_manager_virtual_manager_name_render() {
    $manager_name = get_option('chatgpt_sales_manager_virtual_manager_name', '');
    ?>
    <input type="text" name="chatgpt_sales_manager_virtual_manager_name" value="<?php echo esc_attr($manager_name); ?>" style="width: 100%; max-width: 600px;">
    <?php
}

function chatgpt_sales_manager_virtual_manager_avatar_render() {
    $avatar_url = get_option('chatgpt_sales_manager_virtual_manager_avatar', '');
    ?>
    <input type="text" name="chatgpt_sales_manager_virtual_manager_avatar" value="<?php echo esc_attr($avatar_url); ?>" id="chatgpt_sales_manager_virtual_manager_avatar" style="width: 100%; max-width: 600px;">
    <button type="button" class="button" id="upload_avatar_button"><?php _e('Upload Avatar', 'chatgpt-sales-manager'); ?></button>
    <?php
}

function chatgpt_sales_manager_virtual_manager_greeting_render() {
    $greeting = get_option('chatgpt_sales_manager_virtual_manager_greeting', '');
    ?>
    <textarea name="chatgpt_sales_manager_virtual_manager_greeting" rows="3" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($greeting); ?></textarea>
    <?php
}

add_action('admin_enqueue_scripts', 'chatgpt_sales_manager_enqueue_admin_scripts');

function chatgpt_sales_manager_enqueue_admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('chatgpt_sales_manager_admin_css', plugin_dir_url(__FILE__) . '../css/admin-style.css');
    wp_enqueue_script('chatgpt_sales_manager_admin_js', plugin_dir_url(__FILE__) . '../js/admin-script.js', array('jquery', 'wp-color-picker', 'media-upload', 'thickbox'), null, true);
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

// Function to display messages in the admin panel
function chatgpt_sales_manager_messages_page() {
    global $wpdb;
    $table_name_dialogs = $wpdb->prefix . 'chatgpt_sales_manager_dialogs';
    $table_name_messages = $wpdb->prefix . 'chatgpt_sales_manager_messages';
    
    if (isset($_GET['dialog_id'])) {
        $dialog_id = intval($_GET['dialog_id']);
        $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_messages WHERE dialog_id = %d ORDER BY time ASC", $dialog_id));
        $dialog_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_dialogs WHERE id = %d", $dialog_id));
        
        if (!$dialog_info) {
            echo '<div class="wrap"><h1>' . __('Dialog Not Found', 'chatgpt-sales-manager') . '</h1></div>';
            return;
        }
        
        echo '<div class="wrap">';
        echo '<h1>' . __('ChatGPT Messages', 'chatgpt-sales-manager') . '</h1>';
        echo '<h2>' . sprintf(__('Dialog with %s', 'chatgpt-sales-manager'), esc_html($dialog_info->user_name)) . '</h2>';
        echo '<a href="' . admin_url('admin.php?page=chatgpt-sales-manager-messages') . '">' . __('Back to All Dialogs', 'chatgpt-sales-manager') . '</a>';
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>' . __('Time', 'chatgpt-sales-manager') . '</th><th>' . __('User Name', 'chatgpt-sales-manager') . '</th><th>' . __('Message', 'chatgpt-sales-manager') . '</th><th>' . __('Message Type', 'chatgpt-sales-manager') . '</th></tr></thead>';
        echo '<tbody>';
        foreach ($messages as $message) {
            echo '<tr>';
            echo '<td>' . esc_html($message->time) . '</td>';
            echo '<td>' . esc_html($message->user_name) . '</td>';
            echo '<td>' . esc_html($message->message) . '</td>';
            echo '<td>' . esc_html($message->message_type) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    } else {
        $dialogs = $wpdb->get_results("SELECT * FROM $table_name_dialogs ORDER BY start_time DESC");
        
        echo '<div class="wrap">';
        echo '<h1>' . __('ChatGPT Messages', 'chatgpt-sales-manager') . '</h1>';
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>' . __('Start Time', 'chatgpt-sales-manager') . '</th><th>' . __('User Name', 'chatgpt-sales-manager') . '</th><th>' . __('View Messages', 'chatgpt-sales-manager') . '</th></tr></thead>';
        echo '<tbody>';
        foreach ($dialogs as $dialog) {
            echo '<tr>';
            echo '<td>' . esc_html($dialog->start_time) . '</td>';
            echo '<td>' . esc_html($dialog->user_name) . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=chatgpt-sales-manager-messages&dialog_id=' . $dialog->id) . '">' . __('View Messages', 'chatgpt-sales-manager') . '</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
?>
