<?php

if( !function_exists( 'pixova_lite_body_classes' ) ) {
    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     * @return array
     */

    function pixova_lite_body_classes($classes)
    {

        // Adds a class of group-blog to blogs with more than 1 published author.

        if (is_multi_author()) {
            $classes[] = 'group-blog';
        }

        return $classes;

    }

    add_filter('body_class', 'pixova_lite_body_classes');
}

if( !function_exists( 'pixova_lite_wp_title' ) ) {
    /**
     * Filters wp_title to print a neat <title> tag based on what is being viewed.
     *
     * @param string $title Default title text for current view.
     * @param string $sep Optional separator.
     * @return string The filtered title.
     */

    function pixova_lite_wp_title($title, $sep)
    {

        if (is_feed()) {

            return $title;

        }

        global $page, $paged;

        // Add the blog name
        $title .= get_bloginfo('name', 'display');

        // Add the blog description for the home/front page.
        $site_description = get_bloginfo('description', 'display');
        if ($site_description && (is_home() || is_front_page())) {
            $title .= " $sep $site_description";

        }

        // Add a page number if necessary:

        if ($paged >= 2 || $page >= 2) {
            $title .= " $sep " . sprintf(__('Page %s', 'pixova-lite'), max($paged, $page));
        }

        return $title;

    }

    add_filter('wp_title', 'pixova_lite_wp_title', 10, 2);
}

if( !function_exists( 'pixova_lite_setup_author' ) ) {
    /**
     * Sets the authordata global when viewing an author archive.
     *
     * This provides backwards compatibility with
     * http://core.trac.wordpress.org/changeset/25574
     *
     * It removes the need to call the_post() and rewind_posts() in an author
     * template to print information about the author.
     *
     * @global WP_Query $wp_query WordPress Query object.
     * @return void
     */

    function pixova_lite_setup_author()
    {

        global $wp_query;

        if ($wp_query->is_author() && isset($wp_query->post)) {
            $GLOBALS['authordata'] = get_userdata($wp_query->post->post_author);

        }

    }

    add_action('wp', 'pixova_lite_setup_author');
}

if( !function_exists( 'pixova_lite_prefix_upsell_notice' ) ) {
    /**
     * Display upgrade notice on customizer page
     */
    function pixova_lite_prefix_upsell_notice()
    {

        // Enqueue the script
        wp_enqueue_script(
            'pixova-lite-customizer-upsell',
            get_template_directory_uri() . '/layout/js/upsell/upsell.js',
            array(), '1.0.1',
            true
        );

        // Localize the script
        wp_localize_script(
            'pixova-lite-customizer-upsell',
            'prefixL10n',
            array(
                'prefixURL' => esc_url('https://www.machothemes.com/?edd_action=add_to_cart&download_id=13&discount=DEMO20'),
                'prefixLabel' => __('BUY PRO AND GET 20% OFF', 'pixova-lite'),
                'prefixImageURL' => esc_url( get_template_directory_uri() . '/layout/images/upsell/upgrade-20-off.png')
            )
        );

    }

    add_action('customize_controls_enqueue_scripts', 'pixova_lite_prefix_upsell_notice');
}


// Function to convert hex color codes to rgba 

if( !function_exists( 'pixova_lite_hex2rgba' ) ) {
    function pixova_lite_hex2rgba($color, $opacity = false)
    {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }
}

if ( ! function_exists( 'pixova_lite_post_nav' ) ) {

	/**
	 * Display navigation to next/previous post when applicable.
	 */

	function pixova_lite_post_nav()
	{

		// Don't print empty markup if there's nowhere to navigate.
		$previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
		$next = get_adjacent_post(false, '', false);

		if (!$next && !$previous) {
			return;
		}
		?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e('Post navigation', 'pixova-lite'); ?></h1>

			<div class="nav-links">
				<?php

				previous_post_link('<div class="nav-previous">%link</div>', _x('<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'pixova-lite'));
				next_post_link('<div class="nav-next">%link</div>', _x('%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'pixova-lite'));

				?>
			</div>
			<!-- .nav-links -->
		</nav><!-- .navigation -->

		<?php

	}
}

if ( ! function_exists( 'pixova_lite_content_nav' ) ) {
	/**
	 * Display navigation to next/previous pages when applicable
	 */
	function pixova_lite_content_nav($nav_id)
	{
		global $wp_query, $post;

		// Don't print empty markup on single pages if there's nowhere to navigate.
		if (is_single()) {
			$previous = (is_attachment()) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
			$next = get_adjacent_post(false, '', false);

			if (!$next && !$previous)
				return;
		}

		// Don't print empty markup in archives if there's only one page.
		if ($wp_query->max_num_pages < 2 && (is_home() || is_archive() || is_search()))
			return;

		$nav_class = (is_single()) ? 'post-navigation' : 'paging-navigation';
		?>
		<nav role="navigation" id="<?php echo esc_attr($nav_id); ?>" class="<?php echo $nav_class; ?>">
			<h1 class="screen-reader-text"><?php _e('Post navigation', 'pixova-lite'); ?></h1>

			<?php if (is_single()) : // navigation links for single posts ?>

				<?php previous_post_link('<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'pixova-lite') . '</span> %title'); ?>
				<?php next_post_link('<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x('&rarr;', 'Next post link', 'pixova-lite') . '</span>'); ?>

			<?php elseif ($wp_query->max_num_pages > 1 && (is_home() || is_archive() || is_search())) : // navigation links for home, archive, and search pages ?>

				<?php if (get_next_posts_link()) : ?>
					<div
						class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'pixova-lite')); ?></div>
				<?php endif; ?>

				<?php if (get_previous_posts_link()) : ?>
					<div
						class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', 'pixova-lite')); ?></div>
				<?php endif; ?>

			<?php endif; ?>
			<div class="clear"></div>
		</nav><!-- #<?php echo esc_html($nav_id); ?> -->
		<?php
	}
}

if( ! function_exists( 'pixova_lite_breadcrumbs' ) ) {
    /**
     * Render the breadcrumbs with help of class-breadcrumbs.php
     *
     * @return void
     */
    function pixova_lite_breadcrumbs() {
        $breadcrumbs = new Pixova_Lite_Breadcrumbs();
        $breadcrumbs->get_breadcrumbs();
    }
}

if( !function_exists( 'pixova_lite_fix_responsive_videos' ) ) {
    /*
    /* Add responsive container to embeds
    */
    function pixova_lite_fix_responsive_videos($html)
    {
        return '<div class="pixova-lite-video-container">' . $html . '</div>';
    }

    add_filter('embed_oembed_html', 'pixova_lite_fix_responsive_videos', 10, 3);
    add_filter('video_embed_html', 'pixova_lite_fix_responsive_videos'); // Jetpack
}

if( !function_exists( 'pixova_lite_get_number_of_comments' ) ) {
    /**
     * Simple function used to return the number of comments a post has.
     */
    function pixova_lite_get_number_of_comments($post_id) {

        $num_comments = get_comments_number($post_id); // get_comments_number returns only a numeric value

        if ( comments_open() ) {
            if ( $num_comments == 0 ) {
                $comments = __('No Comments', 'pixova-lite');
            } elseif ( $num_comments > 1 ) {
                $comments = $num_comments . __(' Comments', 'pixova-lite');
            } else {
                $comments = __('1 Comment', 'pixova-lite');
            }
            $write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
        } else {
            $write_comments =  __('Comments are off for this post.', 'pixova-lite');
        }

        return $write_comments;

    }
}