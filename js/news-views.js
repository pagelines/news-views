!function ($) {
    $(document).ready(function($) {
        
        $('.kp-detail').carousel({
            pause: true,
            interval: false
        });

        $('.kp-detail').carousel2({
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

        var new_views = $('.kp-news-views');
        $.each(new_views, function() {
            time = parseInt($(this).attr('data-interval'));
            if(time > 0){
    			testinterval = setInterval(function(){autoplay_newsview()}, time);
            }
        });
    	
    	

    	$('.kp-detail').mouseenter(function(){
    		if($('.kp-news-views').hasClass('pause_slide')){
    			clearInterval(testinterval);
    		}
    	});
    	
    	$('.kp-detail').mouseleave(function(){ 
    		if($('.kp-news-views').hasClass('pause_slide')){
    			testinterval = setInterval(function(){autoplay_newsview()}, time);
    		}
    	});

        $('.kp-list-item').mouseenter(function(){
            if($('.kp-news-views').hasClass('pause_slide')){
                clearInterval(testinterval);
            }
            $('.kp-list-item').removeClass('active');
            $(this).addClass('active').click();
            var $data_target = $($(this).attr('data-target'));
            $data_target.data('carousel').pause();  
            $data_target.data('carousel2').pause();  
        });
        
        $('.kp-list-item').mouseleave(function(){ 
            if($('.kp-news-views').hasClass('pause_slide')){
                testinterval = setInterval(function(){autoplay_newsview()}, time);
            }
        });

    });

    var autoplay_newsview_index = 0;
    function autoplay_newsview() {
        var current_item = $('.kp-list-item.active');
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
                var current_page = parseInt(pagination.find('li.active span.current').text());
                if (0 === first_item.index() % limit) {
                    pagination.find('.prev').click();
                }
                loop = current_page - 2;
                console.log(loop);
                for (var i = 0; i < loop; i++) {
                    pagination.find('.prev').click();
                    console.log(loop);
                    console.log(pagination.find('li.active span.current').text());
                }       
                // first_item.click();
            }
        }
    }
}(window.jQuery);