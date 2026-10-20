(function () {

  jQuery('document').ready(function () {
    var querySelector = '#user_login, #user_pass';
    var inputs = jQuery(querySelector);
    var constClassActive = 'aemi-login-contains-value';
    var constClassInactive = 'aemi-login-no-contains-value';

    function inputClassAddRemove(currentInput) {
      currentInput = jQuery(currentInput);
      var currentParent = currentInput.parent();

      if (currentInput.attr('id') == 'user_pass') {
        currentParent = currentParent.parent();
      }

      currentParent.addClass('aemi-login-contains-input');

      if (!currentInput.val() == '' || currentInput.val() && currentInput.val().replace(/ /g, '') == '') {
        currentParent.addClass(constClassActive);
        currentParent.removeClass(constClassInactive);
      } else {
        currentParent.removeClass(constClassActive);
        currentParent.addClass(constClassInactive);
      }
    }

    function runInputsValidations() {
      for (let index = 0; index < inputs.length; index++) {
        inputClassAddRemove(inputs[index]);
      }
    }

    runInputsValidations();

    setTimeout(function () {
      inputs[0].click();
    }, 100);

    inputs.focus(function () {
      var el = jQuery(this);
      if (el.attr('id') == 'user_pass') {
        el.parent().parent().addClass('aemi-login-contains-input-focus');
      } else {
        el.parent().addClass('aemi-login-contains-input-focus');
      }
    });

    inputs.blur(function () {
      var el = jQuery(this);
      if (el.attr('id') == 'user_pass') {
        el.parent().parent().removeClass('aemi-login-contains-input-focus');
      } else {
        el.parent().removeClass('aemi-login-contains-input-focus');
      }
      runInputsValidations();
    });

  });
})();