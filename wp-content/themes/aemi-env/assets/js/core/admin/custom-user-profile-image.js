jQuery(function ($) {

  var btnRemoveThumbnail = $('#attachment-user-remove-thumbnail');
  var inputSetAttchmentValue = $('#attachment-user-id-thumbnail');
  var elementUrlThumbnail = $('#attachment-user-url-thumbnail');

  $('body').on('click', '#attachment-user-id-btn', function (e) {
    e.preventDefault();

    var uploaderThumbnailImage = wp.media({
      title: admin_custom_profile_script_vars.titleUploadImage,
      library: {
        type: 'image'
      },
      button: {
        text: admin_custom_profile_script_vars.btnTextUploadImage
      },
      multiple: false
    }).on('select', function () {
      try {
        var attachment = uploaderThumbnailImage.state().get('selection').first().toJSON();
        inputSetAttchmentValue.val(attachment.id);
        elementUrlThumbnail.css('backgroundImage', 'url(' + attachment.url + ')');
        btnRemoveThumbnail.css('display', 'block');
      } catch (error) {

      }
    }).open();
  });

  elementUrlThumbnail.click(function (e) {
    btnRemoveThumbnail.css('display', 'none');
    inputSetAttchmentValue.val('');
    elementUrlThumbnail.css('backgroundImage', 'url()');
  });

});