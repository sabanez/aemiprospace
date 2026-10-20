jQuery(function ($) {
  var replaceElementThumbnail = $('.custom-user-profile-thumbnail-img');
  var replaceElementCurrentThumbnailInput = $('.custom-user-profile-current-image-input');
  var replaceElementInfosInput = $('#custom-user-profile-thumbnail-input');
  var replaceElementBtnCancel = replaceElementThumbnail.find('.custom-user-profile-thumbnail__confirm-change-cancel');
  var replaceElementBtnConfirm = replaceElementThumbnail.find('.custom-user-profile-thumbnail__confirm-change-confirm');
  var replaceElementContainerModalChange = replaceElementThumbnail.find('.custom-user-profile-thumbnail__confirm-change-container');
  var defaultThumbnail = replaceElementContainerModalChange.data('urldefaultthumbnail');

  $('body').on('click', '.custom-user-profile-thumbnail-remove', function (e) {
    replaceElementThumbnail.css('backgroundImage', '');
    replaceElementThumbnail.removeClass('custom-user-profile-thumbnail-has-image');
    replaceElementInfosInput.val('');
    replaceElementCurrentThumbnailInput.val('');
    replaceElementContainerModalChange.addClass('active');
  });

  $('body').on('input', '#custom-user-profile-thumbnail-input', function (e) {
    if (this && this.files[0]) {
      var readerFileImage = new FileReader();

      readerFileImage.addEventListener("load", function () {
        var imageInBase64 = readerFileImage.result;
        replaceElementThumbnail.css('backgroundImage', 'url(' + imageInBase64 + ')');
        replaceElementThumbnail.addClass('custom-user-profile-thumbnail-has-image');
      }, false);
      readerFileImage.readAsDataURL(this.files[0]);

      replaceElementContainerModalChange.addClass('active');
    }
  });

  $('body').on('click', '.custom-user-profile-thumbnail-img .custom-user-profile-thumbnail__confirm-change-cancel', function (e) {
    replaceElementContainerModalChange.removeClass('active');

    replaceElementInfosInput.val('');
    replaceElementInfosInput.trigger('input');

    if( defaultThumbnail ){
      replaceElementThumbnail.css('backgroundImage', 'url(' + defaultThumbnail + ')');
      replaceElementThumbnail.addClass('custom-user-profile-thumbnail-has-image');
    } else {
      replaceElementThumbnail.css('backgroundImage', '');
      replaceElementThumbnail.removeClass('custom-user-profile-thumbnail-has-image');
    }
  });

  $('body').on('click', '.custom-user-profile-thumbnail-img .custom-user-profile-thumbnail__confirm-change-confirm', function (e) {
    replaceElementBtnConfirm.removeClass('active');

    var formDataSendNewProfileImage = new FormData( $("form.my-account-form-custom-user-profile")[0] );
    formDataSendNewProfileImage.append('action', 'custom_change_profile_image');
    replaceElementContainerModalChange.removeClass('active');
    $("form.my-account-form-custom-user-profile").addClass('active-loading');

    $.ajax({ 
        url : custom_profile_script_vars.admin_ajax, 
        type: 'POST',
        dataType: 'json', 
        data: formDataSendNewProfileImage,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function( dataReturn ){
          $("form.my-account-form-custom-user-profile").removeClass('active-loading');
          var typeDataReturn = 'false';
          if( dataReturn.success ){
            typeDataReturn = 'success';
          }

          if( $('.custom-user-profile-msg').length > 0 ){
            $('.custom-user-profile-msg').removeClass('active');
            setTimeout(() => {
              $('.custom-user-profile-msg').remove('active');
            }, 300);
          }

          htmlReturnMessage = '<div class="custom-user-profile-msg" data-type="' + typeDataReturn + '">' +  dataReturn.msg + '<div>';

          $('body').append(htmlReturnMessage);

          setTimeout(function(){
            $('.custom-user-profile-msg').addClass('active');
            
            setTimeout(function(){
              $('.custom-user-profile-msg').removeClass('active');
              
              setTimeout(function(){
                $('.custom-user-profile-msg').remove();
              }, 100);
            }, 4000);
          }, 350);
        }
    });
  });

});