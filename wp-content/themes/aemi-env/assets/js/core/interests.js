jQuery(function($) {

    $(document).on('submit', '#form-customer-interests', function(e) {
        e.preventDefault();

        let data = $(this).serialize();
        let alert = $('.alerts');

        $.ajax({
            url: obj.ajaxurl,
            method: 'POST',
            data: {
                nonce: obj.nonce,
                data: data,
                action: 'set_aemi_customer_interests'
            }
        }).done(function(response) {

            // if (response == 200) {
            //     alert.addClass('push-alert alert-type-success');
            //     alert.find('.alert-content').html('Dados Atualizados com Sucesso!');
            // } else {
            //     alert.addClass('push-alert alert-type-failed');
            //     alert.find('.alert-content').html('Ops! Erro ao Atualizar os Dados.');
            // }
            //
            // setTimeout(function() {
            //     alert.removeClass('push-alert alert-type-success alert-type-failed');
            //     alert.find('.alert-content').html('');
            // }, 3000);

            document.location.reload();

        });
    });

});