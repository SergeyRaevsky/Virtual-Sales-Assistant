jQuery(document).ready(function($) {
    // Handle tabs
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });

    // Media uploader for icon image
    var mediaUploader;
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#chatgpt_sales_manager_button_style_icon_url').val(attachment.url);
        });
        mediaUploader.open();
    });

    // Media uploader for manager avatar
    var avatarUploader;
    $('#upload_avatar_button').click(function(e) {
        e.preventDefault();
        if (avatarUploader) {
            avatarUploader.open();
            return;
        }
        avatarUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Avatar',
            button: {
                text: 'Choose Avatar'
            }, multiple: false });
        avatarUploader.on('select', function() {
            var attachment = avatarUploader.state().get('selection').first().toJSON();
            $('#chatgpt_sales_manager_virtual_manager_avatar').val(attachment.url);
        });
        avatarUploader.open();
    });

    // Media uploader for business logo
    var logoUploader;
    $('#upload_logo_button').click(function(e) {
        e.preventDefault();
        if (logoUploader) {
            logoUploader.open();
            return;
        }
        logoUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Logo',
            button: {
                text: 'Choose Logo'
            }, multiple: false });
        logoUploader.on('select', function() {
            var attachment = logoUploader.state().get('selection').first().toJSON();
            $('#chatgpt_sales_manager_business_logo').val(attachment.url);
        });
        logoUploader.open();
    });

    // Initialize color picker
    $('.color-field').wpColorPicker();
});
