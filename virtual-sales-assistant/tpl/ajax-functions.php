<?php
add_action('wp_ajax_nopriv_chatgpt_sales_manager_determine_name_language', 'chatgpt_sales_manager_determine_name_language');
add_action('wp_ajax_chatgpt_sales_manager_determine_name_language', 'chatgpt_sales_manager_determine_name_language');

function chatgpt_sales_manager_determine_name_language() {
if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'chatgpt_sales_manager_nonce')) {
wp_send_json_error(['response' => __('Security check failed.', 'virtual-sales-assistant')]);
}

$user_input = isset($_POST['user_input']) ? sanitize_text_field($_POST['user_input']) : '';

if (empty($user_input)) {
wp_send_json_error(['response' => __('User input is missing.', 'virtual-sales-assistant')]);
}

$openaiApiKey = get_option('chatgpt_sales_manager_api_key');
$model = get_option('chatgpt_sales_manager_model_version', 'gpt-3.5-turbo');

if (empty($openaiApiKey)) {
wp_send_json_error(['response' => __('Error: API key is missing. Please configure it in the plugin settings.', 'virtual-sales-assistant')]);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
'model' => $model,
'messages' => [
['role' => 'system', 'content' => __('You are a helpful assistant.', 'virtual-sales-assistant')],
['role' => 'user', 'content' => sprintf(__('Extract the name and preferred language from this text: "%s". Provide the result in the format \'Name: [name], Language: [language]\'. If the name is not clearly mentioned, just return \'User\'. The language should be in ISO 639-1 code.', 'virtual-sales-assistant'), $user_input)]
],
'max_tokens' => 50,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Content-Type: application/json',
'Authorization: ' . 'Bearer ' . $openaiApiKey,
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
wp_send_json_error(['response' => sprintf(__('Error: %s', 'virtual-sales-assistant'), curl_error($ch))]);
}
curl_close($ch);

$responseData = json_decode($response, true);
if (isset($responseData['error'])) {
wp_send_json_error(['response' => sprintf(__('API Error: %s', 'virtual-sales-assistant'), $responseData['error']['message'])]);
}

$result = $responseData['choices'][0]['message']['content'] ?? 'Name: User, Language: en';
preg_match('/Name:\s*(.*?),\s*Language:\s*(.*)/', $result, $matches);

if (count($matches) === 3) {
$name = trim($matches[1]);
$language = trim($matches[2]);
wp_send_json_success(['response' => "$name | $language"]);
} else {
wp_send_json_success(['response' => 'User | en']);
}
}

add_action('wp_ajax_nopriv_chatgpt_sales_manager', 'chatgpt_sales_manager_handle');
add_action('wp_ajax_chatgpt_sales_manager', 'chatgpt_sales_manager_handle');

function chatgpt_sales_manager_handle() {
if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'chatgpt_sales_manager_nonce')) {
wp_send_json_error(['response' => __('Security check failed.', 'virtual-sales-assistant')]);
}

$context = isset($_POST['context']) ? json_decode(stripslashes($_POST['context']), true) : [];
$dialog_id = isset($_POST['dialog_id']) ? intval($_POST['dialog_id']) : 0;

if (empty($context)) {
wp_send_json_error(['response' => __('Context is missing.', 'virtual-sales-assistant')]);
}

$openaiApiKey = get_option('chatgpt_sales_manager_api_key');
$model = get_option('chatgpt_sales_manager_model_version', 'gpt-3.5-turbo');

if (empty($openaiApiKey)) {
wp_send_json_error(['response' => __('Error: API key is missing. Please configure it in the plugin settings.', 'virtual-sales-assistant')]);
}

global $wpdb;
$table_name_messages = $wpdb->prefix . 'chatgpt_sales_manager_messages';
$table_name_dialogs = $wpdb->prefix . 'chatgpt_sales_manager_dialogs';

// Check if dialog exists, if not, create a new one
if ($dialog_id == 0) {
$user_name = isset($_POST['user_name']) ? sanitize_text_field($_POST['user_name']) : 'User';

$wpdb->insert(
$table_name_dialogs,
array(
'user_name' => $user_name,
)
);

$dialog_id = $wpdb->insert_id;
}

// Saving user message to the database
$user_name = isset($_POST['user_name']) ? sanitize_text_field($_POST['user_name']) : 'User';
$user_message = isset($context[count($context) - 1]['content']) ? sanitize_textarea_field($context[count($context) - 1]['content']) : '';

$wpdb->insert(
$table_name_messages,
array(
'dialog_id' => $dialog_id,
'user_name' => $user_name,
'message' => $user_message,
'message_type' => 'user',
)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
'model' => $model,
'messages' => $context,
'max_tokens' => 150,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Content-Type: application/json',
'Authorization: ' . 'Bearer ' . $openaiApiKey,
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
wp_send_json_error(['response' => sprintf(__('Error: %s', 'virtual-sales-assistant'), curl_error($ch))]);
}
curl_close($ch);

$responseData = json_decode($response, true);
if (isset($responseData['error'])) {
wp_send_json_error(['response' => sprintf(__('API Error: %s', 'virtual-sales-assistant'), $responseData['error']['message'])]);
}

$botResponse = $responseData['choices'][0]['message']['content'] ?? __('No response', 'virtual-sales-assistant');

// Saving bot response to the database
$wpdb->insert(
$table_name_messages,
array(
'dialog_id' => $dialog_id,
'user_name' => 'Bot',
'message' => $botResponse,
'message_type' => 'bot',
)
);

wp_send_json_success(['response' => $botResponse, 'dialog_id' => $dialog_id]);
}