<?php
/*
  Plugin Name: News Views
  Author: Kyle & Irving
  Author URI: http://pagelines.kyle-irving.co.uk/
  Plugin URI: http://pagelines.kyle-irving.co.uk/news-views/
  Version: 2.1.3
  Description: Display a list of posts order by ID, author, title , created date, modified date, random OR comment count
  Class Name: NewsViews
  PageLines: true
  Section: true
  Filter: component
 */

/**
 * IMPORTANT
 * This tells wordpress to not load the class as DMS will do it later when the main sections API is available.
 * If you want to include PHP earlier like a normal plugin just add it above here.
 */
if (!class_exists('NewsViews'))
    return;

/**
 * Start of section class.
 */
class NewsViews extends PageLinesSection {

    function __construct($settings = array()) {
        parent::__construct($settings);
        add_action('init', array(&$this, 'init'));
    }

    function init() {
        load_plugin_textdomain('news_views', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    function section_scripts() {
        global $is_IE;

        if (!wp_script_is('jquery-bootstrap-carousel'))
            wp_enqueue_script('jquery-bootstrap-carousel', $this->base_url . '/js/bootstrap-carousel.js', array('jquery'), NULL, true);


        if (!wp_script_is('jquery-easing'))
            wp_enqueue_script('jquery-easing', $this->base_url . '/js/jquery.easing.1.3.js', array('jquery'), NULL, true);

        if (!wp_script_is('jquery-simplePagination'))
            wp_enqueue_script('jquery-simplePagination', $this->base_url . '/js/jquery.simplePagination.js', array('jquery'), NULL, true);

        if (!wp_script_is('news-views'))
            wp_enqueue_script('news-views', $this->base_url . '/js/news-views.js', array('jquery'), NULL, true);

        if (!wp_style_is('jquery-simplePagination'))
            wp_enqueue_style('jquery-simplePagination', $this->base_url . '/css/jquery-simplePagination.css', array(), NULL);

        if (!wp_style_is('news-views'))
            wp_enqueue_style('news-views', $this->base_url . '/css/news-views.css', array(), NULL);

        $is_use_responsive = $this->opt('is_use_responsive', array('default' => false));
        if ($is_use_responsive) {
            if (!wp_style_is('news-views-responsive'))
                wp_enqueue_style('news-views-responsive', $this->base_url . '/css/news-views-responsive.css', array(), NULL);
        }
    }

    /**
     * Print section javascript into page HEAD area.
     */
    function section_head() {
        $bg_color = pl_hashify($this->opt('background_color', array('default' => '#c9c9c9')));
        $bt_color = pl_hashify($this->opt('button_color', array('default' => '#14739E')));
        $txt_color = pl_hashify($this->opt('text_color', array('default' => '#FFFFFF')));
        ?>
        <style type="text/css">
            .kp-news-views{
                background-color: <?php echo $bg_color; ?>;
            }

            .kp-news-views,
            .kp-news-views *{
                color: <?php echo $txt_color; ?>;
            }

            .kp-list-item,
            .kp-detail{
                background-color: <?php echo $this->adjust_color_lighten_darken($bg_color, 20); ?>;
            }

            .kp-list-item h5.kp-post-title{
            }

            .light-theme a, 
            .light-theme span, 
            .kp-post-categories a,
            .kp-post-readmore a{
            }
            .kp-post-categories a{
                background-color: <?php echo $this->adjust_color_lighten_darken($bt_color, 20); ?>;
            }
            .light-theme a, 
            .light-theme span,
            .kp-post-readmore a{
                background-color: <?php echo $bt_color; ?>;
            }

            .light-theme a:hover, 
            .light-theme span:hover,
            .kp-post-categories a:hover,
            .kp-post-readmore a:hover{
                background-color: <?php echo $txt_color; ?>; 
            }

            .kp-post-categories a:hover{
                color: <?php echo $this->adjust_color_lighten_darken($bt_color, 20); ?>;
            }

            .light-theme a:hover i, 
            .light-theme span:hover i,
            .kp-post-readmore a:hover{
                color: <?php echo $bt_color; ?>;
            }



            .kp-list-item.active{
                background-color: <?php echo $bt_color; ?>;
            }                        
        </style>
        <?php
    }

    /**
     * Register section options.
     */
    function section_opts() {

        $opts = array(
            array(
                'title' => __('Styling', 'news_views'),
                'type' => 'multi',
				'col'	=> 1,
                'opts' => array(
                    array(
                        'key' => 'section-title',
                        'type' => 'text',
                        'label' => __('Title', 'news_views'),
                        'default' => __('News Views', 'news_views'),
                    ),
                    array(
                        'key' => 'text_color',
                        'type' => 'color',
                        'label' => __('Text Color', 'news_views'),
                        'default' => '#FFFFFF'
                    ),
                    array(
                        'key' => 'background_color',
                        'type' => 'color',
                        'label' => __('Background Color', 'news_views'),
                        'default' => '#c9c9c9'
                    ),
                    array(
                        'key' => 'button_text',
                        'type' => 'text',
                        'label' => __('Button Text', 'news_views'),
                        'default' => 'Read more',
                    ),
                    array(
                        'key' => 'button_color',
                        'type' => 'color',
                        'label' => __('Button Color', 'news_views'),
                        'default' => '#14739E'
                    ),
                    array(
                        'key' => 'is_use_responsive',
                        'type' => 'check',
                        'label' => __('Use Reponsive Dedign', 'news_views')
                    ),
                )
            ),
            array(
                'title' => __('Query Arguments', 'news_views'),
                'type' => 'multi',
				'col'	=> 2,
                'opts' => array(
                    array(
                        'key' => 'categories',
                        'type' => 'select_multi',
                        'label' => __('Select Categories', 'news_views'),
                        'default' => array(),
                        'opts' => $this->get_categories(),
                    ),
                    array(
                        'key' => 'number_of_articles',
                        'type' => 'count_select',
                        'label' => __('Number of articles (total)', 'news_views'),
                        'default' => 20,
                        'count_start' => 1,
                        'count_number' => 100
                    ),
                    array(
                        'key' => 'post_per_page',
                        'type' => 'count_select',
                        'label' => __('Number of articles (on a Page)', 'news_views'),
                        'default' => 20,
                        'count_start' => 1,
                        'count_number' => 100
                    ),
                    array(
                        'key' => 'interval',
                        'type' => 'text',
                        'label' => __('Slideshow Speed (millisecond). Set 0 to disable "autoplay"', 'news_views'),
                        'default' => 2000                        
                    ),
                    array(
                        'key' => 'orderby',
                        'type' => 'select_same',
                        'label' => __('Order by', 'news_views'),
                        'default' => 'date',
                        'opts' => array(
                            'ID',
                            'author',
                            'title',
                            'date',
                            'modified',
                            'rand',
                            'comment_count'
                        )
                    )
                )
            ),
            array(
                'title' => __('Extra Options', 'news_views'),
                'type' => 'multi',
				'col'	=> 3,
                'opts' => array(                    
                    array(
                        'key' => 'character_limit_of_excerpt',
                        'type' => 'select_same',
                        'label' => __('Character limit of excerpt', 'news_views'),
                        'default' => 100,
                        'opts' => range(60, 200, 20)
                    ),
                    array(
                        'key' => 'character_limit_of_detail',
                        'type' => 'select_same',
                        'label' => __('Character limit of detail', 'news_views'),
                        'default' => 200,
                        'opts' => range(100, 600, 50)
                    ),
                    array(
                        'key' => 'is_hide_author',
                        'type' => 'check',
                        'label' => __('Hide author', 'news_views')
                    ),
                    array(
                        'key' => 'is_hide_categories',
                        'type' => 'check',
                        'label' => __('Hide categories', 'news_views')
                    ),
                    array(
                        'key' => 'is_hide_date',
                        'type' => 'check',
                        'label' => __('Hide date', 'news_views')
                    ),
					array(
                        'key' => 'is_hover_date_pause',
                        'type' => 'check',
                        'label' => __('Hover Article Pause Slide', 'news_views')
                    ),
                )
            )
        );
        return $opts;
    }

  /**
     * Actual section template.
     */
    function section_template() {
        $title = $this->opt('section-title', array('default' => __('Breaking News', 'news_views')));
        $categories = $this->opt('categories', array('default' => array()));
        $number_of_articles = (int) $this->opt('number_of_articles', array('default' => 20));
        $orderby = $this->opt('orderby');
        $post_per_page = (int) $this->opt('post_per_page', array('default' => 4));
        $interval = (int) $this->opt('interval', array('default' => 2000));
        

        $is_hide_author = $this->opt('is_hide_author', array('default' => false));
        $is_hide_categories = $this->opt('is_hide_categories', array('default' => false));
        $is_hide_date = $this->opt('is_hide_date', array('default' => false));
		
        $is_hover_date_pause = $this->opt('is_hover_date_pause', array('default' => false));
		
        $button_text = $this->opt('button_text', array('default' => __('Readmore', 'news_views')));
        $character_limit_of_excerpt = (int) $this->opt('character_limit_of_excerpt', array('default' => 100));
        $character_limit_of_detail = (int) $this->opt('character_limit_of_detail', array('default' => 200));

        $size_of_thumbnail = 'full';

        $query = array(
            'post_type' => 'post',
            'orderby' => $orderby,
            'post_per_page' => $number_of_articles
        );
        /*
        if (in_array('0', $categories)) {
            unset($categories[0]);
            if (!empty($categories))
                $query['cat'] = implode(',', $categories);
        }
        */
		if (!in_array('0', $categories)) {
            //unset($categories[0]);
            if (!empty($categories))
                $query['cat'] = implode(',', $categories);
        }
        $posts = new WP_Query($query);

        $list = array();
        $details = array();
        $carousel_id = 'news-views-carousel-' . wp_generate_password(6, false);

        $loop_index = 0;
        $page_index = 0;

        if ($posts->have_posts()):
            while ($posts->have_posts()):
                $posts->the_post();
                $post_id = get_the_ID();
                $post_url = get_permalink();
                $post_title = get_the_title();
                $excerpt = get_the_content();
                $categories = get_the_category($post_id);

                if ($loop_index % $post_per_page == 0) {
                    $page_index++;
                }

                $list[$post_id] = sprintf('<li class="kp-list-item %s" data-target="#%s" data-slide-to="%d" data-page-number="%d" style="display:%s;">', ('0' == $loop_index) ? 'active' : '', $carousel_id, $loop_index, $page_index, (1 == $page_index) ? 'block' : 'none');
                $list[$post_id].= sprintf('<h5 class="kp-post-title">%s</h5>', $post_title);
                $list[$post_id].= sprintf('<p class="kp-post-exceprt">%s</p>', $this->get_summary($excerpt, $character_limit_of_excerpt));
                $list[$post_id].= '</li>';


                $details[$post_id] = sprintf('<div class="kp-detail-item item %s">', (0 == $loop_index) ? 'active' : '');
                $details[$post_id] .= '<div class="m-wapper-img">'.get_the_post_thumbnail($post_id, $size_of_thumbnail).'</div>';
                $details[$post_id] .= sprintf('<h4 class="kp-post-title">%s</h4>', $post_title);

                if (!$is_hide_date || !$is_hide_author) {
                    $details[$post_id] .= sprintf('<p class="kp-post-meta">&boxh; %s %s</p>', $is_hide_date ? '' : get_the_date(), $is_hide_author ? '' : __('by:', 'news_views') . ' ' . get_the_author());
                }

                $details[$post_id] .= sprintf('<p class="kp-post-exceprt">%s</p>', $this->get_summary($excerpt, $character_limit_of_detail));

                $details[$post_id] .= '<div class="kp-post-meta-second">';

                $details[$post_id] .= '<div class="kp-half kp-left kp-post-categories">';
                if (!$is_hide_categories) {
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $details[$post_id] .= sprintf('<a href="%s">%s</a>', get_category_link($category), $category->name);
                        }
                    }
                }
                $details[$post_id] .= '</div>';
                $details[$post_id] .= '<div class="kp-half kp-right kp-post-readmore">';
                $details[$post_id] .= sprintf('<a href="%s">%s</a>', $post_url, $button_text);
                $details[$post_id] .= '</div>';
                $details[$post_id] .= '<div class="clear"></div>';
                $details[$post_id] .= '</div>';
                $details[$post_id] .= '</div>';

                $loop_index++;
            endwhile;
        endif;
        ?>
        <div class="kp-news-views <?php if($is_hover_date_pause){ echo "pause_slide";} ?>" data-interval="<?php echo $interval; ?>"  >
            <?php echo empty($title) ? '' : sprintf('<h3 class="kp-section-title">%s</h3>', $title); ?>

            <div class="kp-half kp-left kp-list">
                <ul class="kp-list-items">
                    <?php echo implode('', $list); ?>
                </ul>
                <?php if ($posts->found_posts > $post_per_page): ?>
                    <ul class="kp-list-pagination" data-limit="<?php echo $post_per_page;?>"></ul>
                <?php endif; ?>
            </div>

            <div id="<?php echo $carousel_id; ?>" class="kp-half kp-right kp-detail carousel vertical slide">
                <div class="carousel-inner">
                    <?php echo implode('', $details); ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php
        wp_reset_postdata();
    }

    function get_categories() {
        $categories = get_categories();
        $options = array('0' => array('name' => '-- ALL --'));

        foreach ($categories as $category) {
            $options[$category->term_id] = array('name' => $category->name);
        }
        return $options;
    }

    function adjust_color_lighten_darken($color_code, $percentage_adjuster = 0) {
        $percentage_adjuster = round($percentage_adjuster / 100, 2);
        if (is_array($color_code)) {
            $r = $color_code["r"] - (round($color_code["r"]) * $percentage_adjuster);
            $g = $color_code["g"] - (round($color_code["g"]) * $percentage_adjuster);
            $b = $color_code["b"] - (round($color_code["b"]) * $percentage_adjuster);

            return array("r" => round(max(0, min(255, $r))),
                "g" => round(max(0, min(255, $g))),
                "b" => round(max(0, min(255, $b))));
        } else if (preg_match("/#/", $color_code)) {
            $hex = str_replace("#", "", $color_code);
            $r = (strlen($hex) == 3) ? hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)) : hexdec(substr($hex, 0, 2));
            $g = (strlen($hex) == 3) ? hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)) : hexdec(substr($hex, 2, 2));
            $b = (strlen($hex) == 3) ? hexdec(substr($hex, 2, 1) . substr($hex, 2, 1)) : hexdec(substr($hex, 4, 2));
            $r = round($r - ($r * $percentage_adjuster));
            $g = round($g - ($g * $percentage_adjuster));
            $b = round($b - ($b * $percentage_adjuster));

            return "#" . str_pad(dechex(max(0, min(255, $r))), 2, "0", STR_PAD_LEFT)
                    . str_pad(dechex(max(0, min(255, $g))), 2, "0", STR_PAD_LEFT)
                    . str_pad(dechex(max(0, min(255, $b))), 2, "0", STR_PAD_LEFT);
        }
    }

    function get_summary($excerpt, $lenght = 100) {
        $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);
        $excerpt = substr($excerpt, 0, $lenght);
        $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
        $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));

        return $excerpt;
    }

}
