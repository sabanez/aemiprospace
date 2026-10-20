(function () {
  /*
  */
  jQuery(window).load(function () {

    /**
     * Generate fields to add in repeat list
     * 
     * @param {HTMLElement} elementAdded 
     * @param {Int} itemLoop 
     */
    function aemiReplaceValueNameInLoop(elementAdded, itemLoop) {
      var elementsToReplace = elementAdded.find('input,textarea');
      var elementNumberToReplace = elementAdded.find('.aemi-boxs-repeat__number-text');

      if (elementNumberToReplace.length && elementNumberToReplace[0]) {
        elementNumberToReplace[0].innerText = parseInt(itemLoop) + 1;
      }

      if (elementsToReplace.length) {
        elementsToReplace.each(function (index) {
          var element = jQuery(elementsToReplace[index]);
          var getAttrName = element.prop('name');

          if (getAttrName) {
            var regexNameFields = new RegExp('(\\[[0-9]+\\]|\\[AEMIGenerateFieldsRepeatLoopID\\])(?=\\[)', 'gm');
            var matchsValuesRegex = [...getAttrName.matchAll(regexNameFields)];

            if (matchsValuesRegex && matchsValuesRegex[0] && matchsValuesRegex[0][0]) {
              getAttrName = getAttrName.replace(matchsValuesRegex[0][0], '[' + itemLoop + ']');
              element.prop('name', getAttrName);
            }
          }
        });
      }
    }

    /**
     * Detect click button to add new repeat box
     */
    jQuery('body').on('click', '.aemi-boxs-repeat__remove-element', function () {
      var repeatElement = jQuery(this).closest('.aemi-boxs-repeat-container');
      var repeatElementBox = jQuery(this).closest('.aemi-boxs-repeat');

      repeatElementBox.remove();

      var getBoxsRepeatsInElements = repeatElement.find('.aemi-boxs-repeat');

      if (getBoxsRepeatsInElements.length) {
        getBoxsRepeatsInElements.each(function (index) {
          var element = jQuery(getBoxsRepeatsInElements[index]);
          aemiReplaceValueNameInLoop(element, index);
        });
      }
    })

    /**
     * Adds one more set of related fields in the field list
     */
    jQuery('body').on('click', '.aemi-btn-add-more-section-repeat', function () {
      var repeatElement = jQuery(this).closest('.aemi-boxs-repeat-container');
      var repeatElementId = repeatElement.prop('id');
      var contentElementToInsertRepeat = repeatElement.find('.aemi-boxs-repeat__elements');

      if (window['aemiGenerateFieldsRepeat_' + repeatElementId]) {
        var totalBoxsLoop = repeatElement.find('.aemi-boxs-repeat').length;
        var htmlBaseToReplace = window['aemiGenerateFieldsRepeat_' + repeatElementId].replace(/\&quot\;/g, '"');

        var returnHtmlBase = [
          '<div class="aemi-boxs-repeat">',
          '<div class="aemi-boxs-repeat__number"><span class="aemi-boxs-repeat__number-text">' + (totalBoxsLoop + 1) + '</span></div>',
          '<div class="aemi-boxs-repeat__content">' + htmlBaseToReplace + '</div>',
          '<div class="aemi-boxs-repeat__options"><button type="button" class="aemi-boxs-repeat__remove-element"><span class="dashicons dashicons-trash"></span></button></div>',
          '</div>'
        ].join('');

        var elementAdded = jQuery(returnHtmlBase).appendTo(contentElementToInsertRepeat);
        aemiReplaceValueNameInLoop(elementAdded, totalBoxsLoop);
      }
    });

    /**
     * Thumbnail fields
     * Responsible for managing, controlling and adding the selected image to the fields
     */
    jQuery('body').on('click', '.aemi-boxs-repeat__change-image-btn', function (e) {
      e.preventDefault();
      var currentElement = jQuery(this).closest('.aemi-boxs-repeat__change-image-container');
      var currentThumbnailEl = currentElement.find('.aemi-boxs-repeat__change-image-thumbnail');
      var currentInputEl = currentElement.find('.aemi-boxs-repeat__change-image-input');
      var title = currentElement.data('title');
      var buttonText = currentElement.data('buttontext');

      var uploaderThumbnailImage = wp.media({
        title: title,
        library: {
          type: 'image'
        },
        button: {
          text: buttonText
        },
        multiple: false
      }).on('select', function () {
        try {
          var attachment = uploaderThumbnailImage.state().get('selection').first().toJSON();
          currentInputEl.val(attachment.id);
          currentThumbnailEl.css('backgroundImage', 'url(' + attachment.url + ')');
          currentThumbnailEl.addClass('aemi-boxs-repeat__change-image-thumbnail-selected');
        } catch (error) {

        }
      }).open();

    });

    /**
     * This action will be responsible for removing and clearing the fields that were previously filled by a selected image
     */
    jQuery('body').on('click', '.aemi-boxs-repeat__change-image-container .aemi-boxs-repeat__change-image-btn-remove', function (e) {
      e.preventDefault();

      var currentElement = jQuery(this).closest('.aemi-boxs-repeat__change-image-container');
      var currentThumbnailEl = currentElement.find('.aemi-boxs-repeat__change-image-thumbnail');
      var currentInputEl = currentElement.find('.aemi-boxs-repeat__change-image-input');

      currentThumbnailEl.css('backgroundImage', '');
      currentThumbnailEl.removeClass('aemi-boxs-repeat__change-image-thumbnail-selected');
      currentInputEl.val('');
    });

  });
})();