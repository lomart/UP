jQuery(document).ready(function($) {
  $(".upfaq-button").click(function() {
    if ($(this).next(".upfaq-content").is(":hidden")) {
      $(this).next(".upfaq-content").slideDown("fast");
    } else { if ($(".upfaq-content").is(":visible")) {
      $(this).next(".upfaq-content").slideUp("fast");
    }}
  });
});
