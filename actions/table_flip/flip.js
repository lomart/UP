var smallBreak = 800; // Your small screen breakpoint in pixels
var columns = $('.fliptable tr').length;
var rows = $('.fliptable th').length;

$(document).ready(shapeTable());
$(window).resize(function() {
    shapeTable();
});

function shapeTable() {
    if ($(window).width() < smallBreak) {
        for (i=0;i < rows; i++) {
            var maxHeight = $('.fliptable th:nth-child(' + i + ')').outerHeight();
            for (j=0; j < columns; j++) {
                if ($('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').outerHeight() > maxHeight) {
                    maxHeight = $('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').outerHeight();
                }
              	if ($('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').prop('scrollHeight') > $('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').outerHeight()) {
                    maxHeight = $('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').prop('scrollHeight');
                }
            }
            for (j=0; j < columns; j++) {
                $('.fliptable tr:nth-child(' + j + ') td:nth-child(' + i + ')').css('height',maxHeight);
                $('.fliptable th:nth-child(' + i + ')').css('height',maxHeight);
            }
        }
    } else {
        $('.fliptable td, .fliptable th').removeAttr('style');
    }
}