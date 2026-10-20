jQuery(function ($) {

  var btnRemoveThumbnail = $('#attachment-remove-thumbnail');
  var inputSetAttchmentValue = $('#attachment-id-thumbnail');
  var elementUrlThumbnail = $('#attachment-url-thumbnail');

  $('body').on('click', '#attachment-id-btn', function (e) {
    e.preventDefault();

    var uploaderThumbnailImage = wp.media({
      title: aemi_custom_meta_box_vars.aemiTitleUploadImage,
      library: {
        type: 'image'
      },
      button: {
        text: aemi_custom_meta_box_vars.aemiBtnTextUploadImage
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