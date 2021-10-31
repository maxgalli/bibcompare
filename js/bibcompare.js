$(function() {

    $('#page').tabs();

    $('#page').on('click','h3.title a',function() {
        var d = $(this).parent().next('div');
        if (d.is(':visible')) {
            d.hide();
        } else {
            d.show();
        }
        return false;
    });

    $('#page').on('dblclick','textarea',function() {
        $(this).select();
    });

});