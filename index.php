<?php
/**
 * Plugin Name: MK WordPress Portfolio
 * Tags: portfolio, work portfolio, work filter, portfolio filter
 * Author: Milankumar Kyada
 * Description: This plugin will shows your portfolio as a cool way. 
 * Version: 1.0.0
 */

define('MK_PORTFOLIO_PATH',trailingslashit(plugin_dir_url( __FILE__ )));
require_once 'general.php';

add_action( 'init', array($portfolio,'checkPostType'));
add_action('add_meta_boxes',array($portfolio,'portfolioMeta'));
add_action('save_post',array($portfolio,'savePortfolioURL'));
add_shortcode( 'MK_PORTFOLIO', array($portfolio,'generateShortCode') );
add_action( 'wp_enqueue_scripts',array($portfolio,'addIsotopQuery'));