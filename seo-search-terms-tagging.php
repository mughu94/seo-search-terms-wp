<?php
/*
Plugin Name: SEO Search Terms Tagging
Plugin URI: https://mughu.biz.id
Description: A plugin to tag incoming search terms for better SEO.
Version: 1.0
Author: Muhamad Ghufron
Author URI: https://mughu.biz.id
License: GPL2
*/


// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin directory
define( 'SEO_STT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include admin panel
require_once( SEO_STT_PLUGIN_DIR . 'admin/admin-panel.php' );
require_once( SEO_STT_PLUGIN_DIR . 'search-terms-widget.php' );

// Hook into WordPress
add_action( 'wp_footer', 'seo_stt_capture_search_terms' );

function seo_stt_capture_search_terms() {
    if ( is_search() ) {
        $search_query = get_search_query();
        if ( ! empty( $search_query ) ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'seo_stt_terms';
            $search_term = sanitize_text_field( $search_query );

            // Check if search term already exists
            $existing_term = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM $table_name WHERE search_term = %s", $search_term
            ) );

            if ( $existing_term ) {
                // Increment the count for the existing search term
                $wpdb->update(
                    $table_name,
                    array(
                        'count' => $existing_term->count + 1,
                        'date' => current_time( 'mysql' )
                    ),
                    array( 'search_term' => $search_term )
                );
            } else {
                // Insert new search term
                $wpdb->insert(
                    $table_name,
                    array(
                        'search_term' => $search_term,
                        'count' => 1,
                        'date' => current_time( 'mysql' )
                    )
                );
            }
        }
    }
}


// Activation hook
register_activation_hook( __FILE__, 'seo_stt_activate' );

function seo_stt_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'seo_stt_terms';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        search_term varchar(255) NOT NULL,
        count mediumint(9) NOT NULL DEFAULT 1,
        date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY search_term (search_term)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // Register the widget
    register_seo_stt_widget();
}


