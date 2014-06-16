<?php

/*
Plugin Name:    Forwarded Host URLs
Description:    Forces WordPress to build urls using the X-Forwarded-Host header, if it exists.
Author:         blahed, nwah
Author URI:     https://github.com/50east
Plugin URI:     https://github.com/50east/wp-forwarded-host-urls
Version:        0.0.6
*/

function has_forwarded_host() {
  return array_key_exists('HTTP_X_FORWARDED_HOST', $_SERVER);
}

function forwarded_host() {
  return $_SERVER['HTTP_X_FORWARDED_HOST'];
}

function forwarded_base() {
  $_forwarded_host = forwarded_host();
  return "//$_forwarded_host";
}

function apply_forwarded_host() {
  if ( !has_forwarded_host() )
    return false;
  else
    return forwarded_base();
}

function replace_with_forwarded_host($url, $path = '') {
  if ( !has_forwarded_host() )
    return $url;
  else
    return preg_replace('!https?://[a-z0-9.:-]*!', '//' . forwarded_host(), $url);
}

function cancel_canonical_redirect() {
  return false;
}

function replace_content_urls($content, $post_id = NULL) {
  $forwarded_host_url = get_option("forwarded_host_url");

  if ( !has_forwarded_host() )
    return $content;
  else
  return preg_replace('!https?://((127|10|172|0)(\.\d{1,3}){3}|192\.168(\.\d{1,3}){2}|localhost|[\w\-]+.dev|[\w\-]+.local)(?::[0-9]+)?!', '//' . forwarded_host(), $content);
}

function set_urls_to_forwarded_host() {
  // referenced from http://sparanoid.com/work/relative-url/
  if ( is_feed() || get_query_var( 'sitemap' ) )
    return;
  $filters = array(
    'post_link',
    'post_type_link',
    'page_link',
    'attachment_link',
    'get_shortlink',
    'post_type_archive_link',
    'get_pagenum_link',
    'get_comments_pagenum_link',
    'term_link',
    'search_link',
    'day_link',
    'month_link',
    'year_link',
    'option_siteurl',
    'blog_option_siteurl',
    'option_home',
    'admin_url',
    'home_url',
    'includes_url',
    'site_url',
    'site_option_siteurl',
    'network_home_url',
    'network_site_url',
    'get_the_author_url',
    'get_comment_link',
    'wp_get_attachment_image_src',
    'wp_get_attachment_thumb_url',
    'wp_get_attachment_url',
    'wp_login_url',
    'wp_logout_url',
    'wp_lostpassword_url',
    'get_stylesheet_uri',
    'get_locale_stylesheet_uri',
    'script_loader_src',
    'style_loader_src',
    'get_theme_root_uri',
    'stylesheet_uri',
    'template_directory_uri',
    'stylesheet_directory_uri'
  );

  foreach ( $filters as $filter ) {
    add_filter( $filter, 'replace_with_forwarded_host' );
  }
}

// add_filter('pre_option_home', 'apply_forwarded_host');
// add_filter('pre_option_siteurl', 'apply_forwarded_host');
// add_filter('pre_option_url', 'apply_forwarded_host');
add_filter('redirect_canonical', 'cancel_canonical_redirect');
add_filter('the_content', 'replace_content_urls');
add_filter( 'content_edit_pre', 'replace_content_urls');
add_action( 'template_redirect', 'set_urls_to_forwarded_host' );