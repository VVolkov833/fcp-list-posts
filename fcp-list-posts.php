<?php
/*
Plugin Name: FCP List Posts
Description: Use shortcode to add a list of posts, specify by post-type, order, meta and template
Version: 1.2.11
Requires at least: 4.7
Requires PHP: 7.0.0
Author: Firmcatalyst, Vadim Volkov
Author URI: https://firmcatalyst.com
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: fcplp
Domain Path: /languages
GitHub Plugin URI: https://github.com/VVolkov833/fcp-list-posts
*/

defined( 'ABSPATH' ) || exit;

add_shortcode( 'fcp-posts', function($atts = []) {

    $allowed = [
        'type' => 'post',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_key' => '',
        'meta_type' => '',
        'ppp' => 10,
        'offset' => 0,
		'post-in' => '',
        'class' => '',
        'template' => '',
        'before' => '',
        'after' => '',
        'async' => false, // ++
        'firstscreen' => false // ++
    ];

    $meta = []; // ++ check if secured here ++ add not option to pick not featured
    foreach ( $atts as $k => $v ) {
        if ( strpos( $k, 'meta-' ) !== 0 ) { continue; }
        $meta[ substr( $k, 5 ) ] = $v;
    }
    
    $atts = shortcode_atts( $allowed, $atts ); // ++ add that modifying function of mine to change a="" to just a
    
    $atts['type'] = array_map( 'trim', explode( ',', $atts['type'] ) );
	$atts['post-in'] = $atts['post-in'] ? array_map( 'trim', explode( ',', $atts['post-in'] ) ) : null;

    $args = [
        'post_type'        => $atts['type'],
        'orderby'          => $atts['orderby'],
        'order'            => $atts['order'],
        'posts_per_page'   => $atts['ppp'],
        'offset'           => $atts['offset'],
		'post__in'		   => $atts['post-in'],
        'post_status'      => 'publish'
    ];

    if ( $atts['meta_key'] ) {
        $args['meta_key'] = $atts['meta_key'];
    }
    if ( $atts['meta_type'] ) {
        $args['meta_type'] = $atts['meta_type'];
    }

    if ( count( $meta ) ) {
        $args['meta_query'] = [ 'relation' => 'AND' ];
        
        foreach ( $meta as $k => $v ) {
            if ( $v ) {
                $args['meta_query'][] = [
                    'key' => $k,
                    'value' => $v
                ];
                continue;
            }
            $args['meta_query'][] = [
                'key' => $k,
                'compare' => 'EXISTS'
            ];
        }
    }

    $wp_query = new WP_Query( $args );
    
    if ( !$wp_query->have_posts() ) { return ''; }

    ob_start();

    $dir = __DIR__ . '/templates/';
    $url = plugin_dir_url( __FILE__ ) . 'templates/';
    
    if ( $atts['template'] && is_file( $dir . $atts['template'] . '.php' ) ) { // ++check security here!!!

        echo $atts['before'] ? $atts['before'] : '<div class="fcp-list-'.$atts['template'] . ( $atts['class'] ? ' '.$atts['class'] : '' ).'">'."\n";
        while ( $wp_query->have_posts() ) {
            $wp_query->the_post();
            include( $dir . $atts['template'] . '.php' );
        }
        echo $atts['after'] ? $atts['after'] : '</div>' . "\n";
        
        if ( is_file( $dir . $atts['template'] . '.css' ) ) {
            wp_enqueue_style(
                'fcp-list-' . $atts['template'],
                $url . $atts['template'] . '.css',
                [],
                time()
            );
        }

    } else {
    
        echo $atts['before'] ? $atts['before'] : '<ul'.( $atts['class'] ? ' class="'.$atts['class'].'"' : '' ).'>'."\n";
        while ( $wp_query->have_posts() ) {
            $wp_query->the_post();
            echo '<li><a rel="bookmark" href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>' . "\n";
        }
        echo $atts['after'] ? $atts['after'] : '</ul>' . "\n";

    }

    $content = ob_get_contents();
    ob_end_clean();

    wp_reset_query();
    return $content;
});