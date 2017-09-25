jQuery(function($) {
  function sameheight() {
    $('.sameheight').each(function(index) {
      var maxHeight = 0;
      $(this).children().each(function(index) {
        if($(this).height() > maxHeight)
        maxHeight = $(this).height();
      });
      $(this).children().height(maxHeight);
    });
  }

  $(window).bind("load", sameheight);
  $(window).bind("resize", sameheight);
});
