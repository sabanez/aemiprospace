jQuery(function($) {

    $(document).on('submit', '#form-curstomer-subscriptions', function(e) {
        e.preventDefault();
        
        if( $('#form-curstomer-subscriptions button[name="save_account_details"]').attr("disabled") ){

        } else {

            $('#form-curstomer-subscriptions button[name="save_account_details"]').prop('disabled', true);
            $('#form-curstomer-subscriptions button[name="save_account_details"]').attr('disabled', true);
    
            let serializedDataTemp = '';

            if( $(this).serialize() ){
                $(this).serialize().split('&').forEach(element => {
                    let tempData = element.split('=');

                    if( tempData && tempData[1] && tempData[1] != 'no' ){
                        serializedDataTemp += ( serializedDataTemp ? '&' : '' ) + 'subscriptions_option%5B%5D=' + tempData[1];
                    }
                });
            }

            $.ajax({
                url: obj.ajaxurl,
                method: 'POST',
                data: {
                    nonce: obj.nonce,
                    data: serializedDataTemp,
                    action: 'set_aemi_customer_subscriptions'
                }
            }).done(function (response) {
                window.location.href += "?success=1";
            });
        }
    });

    if( $('#form-curstomer-subscriptions input').length > 0 ){
        $('#form-curstomer-subscriptions input').change(function(){
            $('#form-curstomer-subscriptions button[name="save_account_details"]').removeProp('disabled', true);
            $('#form-curstomer-subscriptions button[name="save_account_details"]').removeAttr('disabled', true);
            
        })
    }
});