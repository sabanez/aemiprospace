jQuery(document).ready(function () {
  const mobileMenu = jQuery("#mobileMenu");
  const profileDropdownTrigger = jQuery("#profileDropdownTrigger");
  const profileDropdown = jQuery("#profileDropdown");
  const headerSearchIcon = jQuery("#searchIcon");
  const headerSearchInput = jQuery("#searchInputHolder");

  profileDropdownTrigger.click(() => profileDropdown.toggle());
  headerSearchIcon.click(() => headerSearchInput.toggleClass("visible"));

  jQuery("#menuBar").click(() => {
    mobileMenu.addClass("showBar");
  });
  jQuery("#closeIcon").click(function () {
    mobileMenu.removeClass("showBar");
  });

  jQuery("#updatePwd").click(function (event) {
    event.stopPropagation();
    jQuery("#updateInfoPwd").addClass("showModal");
  });
  jQuery("#closeIconP").click(function (event) {
    event.stopPropagation();
    jQuery("#updateInfoPwd").removeClass("showModal");
  });

  jQuery("#updateInfo").click(function (event) {
    event.stopPropagation();
    jQuery("#updateInfoModal").addClass("showModal");
  });
  jQuery("#closeIconI").click(function (event) {
    event.stopPropagation();
    jQuery("#updateInfoModal").removeClass("showModal");
  });
  //  // Close modal when clicking anywhere outside of it
  //  jQuery(document).click(function(event) {
  //   if (jQuery(event.target).closest("#updateInfoModal").length) {
  //     jQuery("#updateInfoModal").removeClass("showModal");
  //   }
  // });
  // bill
  jQuery("#billAdd").click(function (event) {
    event.stopPropagation();
    jQuery("#billingAdd").addClass("showModal");
  });
  jQuery("#closeIconS").click(function (event) {
    event.stopPropagation();
    jQuery("#billingAdd").removeClass("showModal");
  });
  jQuery("#shippAdd").click(function (event) {
    event.stopPropagation();
    jQuery("#shippingAdd").addClass("showModal");
  });
  jQuery("#closeIconB").click(function (event) {
    event.stopPropagation();
    jQuery("#shippingAdd").removeClass("showModal");
  });
  jQuery("#expBtn").click(function (event) {
    event.stopPropagation();
    jQuery("#experienceModal").addClass("showModal");
  });
  jQuery("#expClose").click(function (event) {
    event.stopPropagation();
    jQuery("#experienceModal").removeClass("showModal");
  });

  jQuery("#myIns").click(function () {
    jQuery("#lefSid").addClass("showSideBar");
  });
  jQuery("#closeIconL").click(function () {
    jQuery("#lefSid").removeClass("showSideBar");
  });

  jQuery(document).click(function (e) {
    if (!jQuery(e.target).closest(".dropdown").length) {
      jQuery(".dropdown-menu").hide();
    }
  });
  // Show modal when clicking the "activeTittle" element
  jQuery("#activeTittle").click(function (event) {
    event.stopPropagation(); // Prevent the click from propagating to the document
    jQuery("#cancelPopup").addClass("showModal");
  });

  // Close modal when clicking the "crossIcon" element
  jQuery("#crossIcon").click(function (event) {
    event.stopPropagation(); // Prevent the click from propagating to the document
    jQuery("#cancelPopup").removeClass("showModal");
  });

  const $modal = jQuery("#claimPointPopup");

  // Show modal when clicking the "claimPointsBtn" button
  jQuery("#claimPointsBtn").click(function (event) {
    event.stopPropagation(); // Prevent the click from propagating to the document
    $modal.addClass("showModal");
  });

  // Close modal when clicking the "crossIconClaim" element
  jQuery("#crossIconClaim").click(function (event) {
    event.stopPropagation(); // Prevent the click from propagating to the document
    $modal.removeClass("showModal");
  });

  // Close modal when clicking anywhere outside of it
  jQuery(document).on("click", ".showModal", function (event) {
    console.log("called");
    if (
      $modal.hasClass("showModal") &&
      !jQuery(event.target).closest("#claimPointPopup").length
    ) {
      $modal.removeClass("showModal");
    }
  });

  // Prevent clicks inside the modal from closing it
  $modal.on("click", function (event) {
    event.stopPropagation();
  });
  var currentUrl = window.location.pathname; // Get the current URL path

  // Find the navigation link with matching href and add 'active' class
  var activeStepLink = jQuery('.menu a[href="' + currentUrl + '"]');

  activeStepLink.addClass("active");

  var currentMenuUrl = window.location.pathname; // Get the current URL path

  // Find the navigation link with matching href and add 'active' class
  var activeMenuLink = jQuery('.naMenu a[href="' + currentMenuUrl + '"]');

  activeMenuLink.addClass("activeHead");
});

function activeTab(evt, id) {
  // Get all elements with class="tablinks" and remove the class "active"
  let tabactive = document.getElementsByClassName("TabButtonSelected");
  tabactive[0].className = tabactive[0].className.replace(
    " TabButtonSelected",
    ""
  );

  document.getElementById(id).style.display = "block";
  evt.currentTarget.className += " TabButtonSelected";

  displaySection(evt, id);
}

function displaySection(evt, id) {
  let tabactive = document.getElementsByClassName("tab-section");
  tabactive[0].className = tabactive[0].className.replace(
    " d-chart-show",
    "d-chart-n"
  );
  // add below line of codes
  [...document.querySelectorAll("div.tab-section")].forEach(
    (item) => (item.style.display = "none")
  );
  document.getElementById("Section" + id).style.display = "block";
  evt.currentTarget.className += " d-chart-show";
}
// radio
var $radioButtons = jQuery("input:radio");
$radioButtons.change(function () {
  var anyRadioButtonHasValue = false;
  $radioButtons.each(function () {
    if (this.checked) {
      anyRadioButtonHasValue = true;
      return false;
    }
  });

  if (anyRadioButtonHasValue) {
    // else is kind of redundant unless you somehow can clear the radio button value
    jQuery("#button11").attr("disabled", "disabled");
  }
});
var closeBtn = document.getElementById("close-btn");
var openBtn = document.getElementById("open-btn");

if( closeBtn ){
  closeBtn.addEventListener("click", function () {
    const openRows = document.querySelectorAll(".open-case");
    const closeRows = document.querySelectorAll(".close-case");
  
    closeBtn.classList.add("active");
    openBtn.classList.remove("active");
  
    openRows.forEach((row) => (row.style.display = "none"));
    closeRows.forEach((row) => (row.style.display = "table-row"));
  });
}

if( openBtn ){
  openBtn.addEventListener("click", function () {
    const openRows = document.querySelectorAll(".open-case");
    const closeRows = document.querySelectorAll(".close-case");
  
    closeBtn.classList.remove("active");
    openBtn.classList.add("active");
  
    openRows.forEach((row) => (row.style.display = "table-row"));
    closeRows.forEach((row) => (row.style.display = "none"));
  });
}
