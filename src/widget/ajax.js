

function n2goAjaxFormSubmit(me) {
    jQuery.post(n2go_ajax_script.ajaxurl, 
        jQuery(me.form).serialize(),
        function (response) {
            var data = JSON.parse(response),
                messageElement = jQuery(me.form.parentElement);

            if (data.success) {
                messageElement.html(data.message);
            } else {
                messageElement.find('.message').text(data.message);
            }
        }
    );
}
