jQuery(document).ready(function($) {
    var businessInfo = chatgpt_sales_manager.business_info;
    var managerName = chatgpt_sales_manager.manager_name;
    var greeting = chatgpt_sales_manager.greeting;
    var avatarUrl = chatgpt_sales_manager.avatar_url;
    var context = [{ role: 'system', content: businessInfo }];
    var userName = '';
    var userLanguage = '';

    // Toggle chat popup visibility
    $('#chat-button, #chat-close').click(function() {
        $('#chat-popup').toggle();
    });

    // Maximize/restore chat popup
    $('#chat-expand').click(function() {
        $('#chat-popup').toggleClass('chat-fullscreen');
        $('#chat-expand').toggleClass('dashicons-editor-expand dashicons-editor-contract');
    });

    // Send message to ChatGPT
    $('#send-button').click(function() {
        var userInput = $('#user-input').val();
        if (userInput.trim() !== '') {
            addMessage('user', userInput, userName || chatgpt_sales_manager.you_label);
            $('#user-input').val('');
            context.push({ role: 'user', content: userInput });

            if (!userName) {
                determineUserNameAndLanguage(userInput, function(name, language) {
                    userName = name || chatgpt_sales_manager.default_user_name;
                    userLanguage = language || 'en';
                    context.push({ role: 'user', content: userInput });
                    $.ajax({
                        url: chatgpt_sales_manager.ajax_url,
                        type: 'post',
                        data: {
                            action: 'chatgpt_sales_manager',
                            security: chatgpt_sales_manager.nonce,
                            context: JSON.stringify(context)
                        },
                        success: function(response) {
                            if (response.success) {
                                addMessage('bot', response.data.response, managerName);
                                context.push({ role: 'assistant', content: response.data.response });
                            } else {
                                addMessage('bot', chatgpt_sales_manager.error_message + response.data.response, managerName);
                            }
                        },
                        error: function() {
                            addMessage('bot', chatgpt_sales_manager.generic_error_message, managerName);
                        }
                    });
                });
            } else {
                $.ajax({
                    url: chatgpt_sales_manager.ajax_url,
                    type: 'post',
                    data: {
                        action: 'chatgpt_sales_manager',
                        security: chatgpt_sales_manager.nonce,
                        context: JSON.stringify(context)
                    },
                    success: function(response) {
                        if (response.success) {
                            addMessage('bot', response.data.response, managerName);
                            context.push({ role: 'assistant', content: response.data.response });
                        } else {
                            addMessage('bot', chatgpt_sales_manager.error_message + response.data.response, managerName);
                        }
                    },
                    error: function() {
                        addMessage('bot', chatgpt_sales_manager.generic_error_message, managerName);
                    }
                });
            }
        }
    });

    function addMessage(sender, text, name) {
        var senderAvatarUrl = sender === 'bot' ? avatarUrl : '';
        var senderName = name || (sender === 'bot' ? managerName : chatgpt_sales_manager.you_label);
        var messageClass = sender === 'bot' ? 'bot' : 'user';

        var messageHtml = '<div class="message-container ' + messageClass + '">' +
            '<div class="avatar">' + (sender === 'bot' ? '<img src="' + senderAvatarUrl + '" alt="' + chatgpt_sales_manager.bot_avatar_alt + '">' : '<div class="user-avatar"></div>') + '</div>' +
            '<div class="content">' +
            '<div class="name">' + senderName + '</div>' +
            '<div class="text">' + text + '</div>' +
            '</div>' +
            '</div>';
        
        $('#chat-box').append(messageHtml);
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }

    function determineUserNameAndLanguage(userInput, callback) {
        $.ajax({
            url: chatgpt_sales_manager.ajax_url,
            type: 'post',
            data: {
                action: 'chatgpt_sales_manager_determine_name_language',
                security: chatgpt_sales_manager.nonce,
                user_input: userInput
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data.response.split('|');
                    var name = data[0].trim();
                    var language = data[1].trim();
                    callback(name, language);
                } else {
                    callback(null, null);
                }
            },
            error: function() {
                callback(null, null);
            }
        });
    }
});
