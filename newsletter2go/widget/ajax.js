

function n2goAjaxFormSubmit() {
    jQuery.post(n2go_ajax_script.ajaxurl, 
        jQuery("#n2goForm").serialize(),
        function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery("#n2goResponseArea").html(data.message);
            } else {
                jQuery("#n2goResponseArea").find('.message').text(data.message);
            }
        }
    );
}