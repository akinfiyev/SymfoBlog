$(document).on('click', 'button.ajax-like', function(e){
    that = $(this);
    var $link = $(e.currentTarget);
    $.ajax({
        url: $link.attr('data-link'),
        type: "GET",
        async: true,
        success: function (data)
        {
            if (data == 0)
                $("span#likes-count-"+$link.attr('data-id')).html('');
            else
                $("span#likes-count-"+$link.attr('data-id')).html(data);
        }
    });
    return false;
});