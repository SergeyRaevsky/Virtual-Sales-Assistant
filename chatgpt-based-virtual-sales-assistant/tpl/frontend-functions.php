<?php
// Include AJAX handlers from ajax-functions.php
include_once plugin_dir_path(__FILE__) . 'ajax-functions.php';

// Add chat interface
function chatgpt_sales_manager_add_chat_interface() {
    $button_style = get_option('chatgpt_sales_manager_button_style', [
        'border_radius' => '50%',
        'background_color' => '#0084ff',
        'icon_color' => '#ffffff',
        'icon_url' => ''
    ]);
    $manager_name = get_option('chatgpt_sales_manager_virtual_manager_name', __('Manager', 'chatgpt-sales-manager'));
    $avatar_url = get_option('chatgpt_sales_manager_virtual_manager_avatar', '');
    $greeting = get_option('chatgpt_sales_manager_virtual_manager_greeting', __('Hello! How can I help you today? Please tell me your name.', 'chatgpt-sales-manager'));
    $business_logo = get_option('chatgpt_sales_manager_business_logo', '');
    $chat_title = get_option('chatgpt_sales_manager_chat_title', __('Chat', 'chatgpt-sales-manager'));
    ?>
    <div id="chat-button" style="border-radius: <?php echo esc_attr($button_style['border_radius']); ?>; background-color: <?php echo esc_attr($button_style['background_color']); ?>;">
        <?php if (!empty($button_style['icon_url'])): ?>
            <img src="<?php echo esc_url($button_style['icon_url']); ?>" alt="<?php esc_attr_e('Chat Icon', 'chatgpt-sales-manager'); ?>" style="width: 24px; height: 24px; fill: <?php echo esc_attr($button_style['icon_color']); ?>;">
        <?php else: ?>
            <span style="color: <?php echo esc_attr($button_style['icon_color']); ?>;">💬</span>
        <?php endif; ?>
    </div>
    <div id="chat-popup" style="display: none;">
        <div id="chat-container">
            <div id="chat-header">
                <?php if (!empty($business_logo)): ?>
                    <img src="<?php echo esc_url($business_logo); ?>" alt="<?php esc_attr_e('Business Logo', 'chatgpt-sales-manager'); ?>" style="height: 40px; margin-right: 10px;">
                <?php endif; ?>
                <span><?php echo esc_html($chat_title); ?></span>
                <span style="flex-grow: 1;"></span>
                <span class="chat-header-icon dashicons dashicons-editor-expand" id="chat-expand"></span>
                <span class="chat-header-icon dashicons dashicons-no-alt" id="chat-close"></span>
            </div>
            <div id="chat-box">
                <div class="message-container bot">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php esc_attr_e('Bot Avatar', 'chatgpt-sales-manager'); ?>" class="avatar">
                    <div class="content">
                        <div class="name"><?php echo esc_html($manager_name); ?></div>
                        <div class="text"><?php echo esc_html($greeting); ?></div>
                    </div>
                </div>
            </div>
            <div id="chat-input-container">
                <input type="text" id="user-input" placeholder="<?php esc_attr_e('Type a message...', 'chatgpt-sales-manager'); ?>" />
                <button id="send-button"><?php _e('Send', 'chatgpt-sales-manager'); ?></button>
            </div>
        </div>
    </div>
    <?php
}
