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
                prevText: '<i class="iii icon icon-2x icon-angle-left "></i>',
                nextText: '<i class="iii icon icon-2x icon-angle-right "></i>',
                onInit: function() {
                },
                onPageClick: function(page_number, event) {
                    objs_show = ".kp-list-item[data-page-number=" + page_number + "]";                    
                    $('.kp-list-item').hide(0, function() {
                        $(objs_show).show();
                    });
                    $(objs_show).first().click();
                }
            });
        });
    }

    var new_views = jQuery('.kp-news-views');
    jQuery.each(new_views, function() {
        time = parseInt(jQuery(this).attr('data-interval'));
        if(time > 0){
			testinterval = setInterval(function(){autoplay_newsview()}, time);
        }
    });
	
	

	jQuery('.kp-detail').mouseenter(function(){
		if(jQuery('.kp-news-views').hasClass('pause_slide')){
			clearInterval(testinterval);
		}
	});
	
	jQuery('.kp-detail').mouseleave(function(){ 
		if(jQuery('.kp-news-views').hasClass('pause_slide')){
			testinterval = setInterval(function(){autoplay_newsview()}, time);
		}
	});

    jQuery('.kp-list-item').mouseenter(function(){
        if(jQuery('.kp-news-views').hasClass('pause_slide')){
            clearInterval(testinterval);
        }
        $('.kp-list-item').removeClass('active');
        $(this).addClass('active').click();
    });
    
    jQuery('.kp-list-item').mouseleave(function(){ 
        if(jQuery('.kp-news-views').hasClass('pause_slide')){
            testinterval = setInterval(function(){autoplay_newsview()}, time);
        }
    });

});

var autoplay_newsview_index = 0;
function autoplay_newsview() {
    var current_item = jQuery('.kp-list-item.active');
    var parent = current_item.parent();
    var last_item = parent.find('.kp-list-item').last();
    var wrap = parent.parents('.kp-left');
    var pagination = wrap.find('.kp-list-pagination');
    var limit = parseInt(pagination.attr('data-limit'));



    if (current_item.length > 0) {
        if (current_item.index() < last_item.index()) {
            var next_item = current_item.next();
            next_item.click();

            if (0 === next_item.index() % limit) {
                pagination.find('.next').click();
            }

        } else {
            var first_item = parent.find('.kp-list-item').first();
            if (0 === first_item.index() % limit) {
                pagination.find('.prev').click();
            }
            first_item.click();
        }
    }
}