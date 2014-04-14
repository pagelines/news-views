jQuery(document).ready(function($) {

    $('.kp-detail').carousel({
        pause: true,
        interval: false
    });

    $('.kp-list-item').live('click', function(event) {
        $('.kp-list-item').removeClass('active');
        $(this).addClass('active');
    });

    paginations = $('.kp-list-pagination');
    if (paginations.length > 0) {
        $.each(paginations, function(index, item) {
            wrap = $(this).parents('.kp-news-views');

            $('.kp-list-pagination').pagination({
                items: wrap.find('li.kp-list-item').length,
                itemsOnPage: wrap.find('li.kp-list-item:visible').length,
                edges: 0,
                cssStyle: 'light-theme',
                prevText: '<i class="kp-icon-arrow-left"></i>',
                nextText: '<i class="kp-icon-arrow-right"></i>',
                onInit: function() {                    
                },
                onPageClick: function(page_number, event) {
                    objs_show = ".kp-list-item[data-page-number=" + page_number + "]";
                    $('.kp-list-item').hide(0, function() {
                        $(objs_show).show();
                    });
                }
            });
        });
    }
});