<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add admin menu
add_action( 'admin_menu', 'seo_stt_admin_menu' );

function seo_stt_admin_menu() {
    add_menu_page(
        'SEO Search Terms',
        'Search Terms',
        'manage_options',
        'seo-search-terms',
        'seo_stt_admin_page',
        'dashicons-search',
        20
    );
}

function seo_stt_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'seo_stt_terms';

    // Check if the form has been submitted
    if ( isset( $_POST['seo_stt_remove_all_terms'] ) && check_admin_referer( 'seo_stt_remove_all_terms_action', 'seo_stt_remove_all_terms_nonce' ) ) {
        // Remove all search terms
        $wpdb->query( "DELETE FROM $table_name" );
        echo '<div class="updated"><p>All search terms have been removed.</p></div>';
    }

    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY date DESC" );
    ?>
    <div class="wrap">
        <h1>SEO Search Terms</h1>

        <form method="post" action="">
            <?php wp_nonce_field( 'seo_stt_remove_all_terms_action', 'seo_stt_remove_all_terms_nonce' ); ?>
            <input type="hidden" name="seo_stt_remove_all_terms" value="1">
            <p>
                <input type="submit" class="button button-primary" value="Remove All Search Terms">
            </p>
        </form>

        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col">ID</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Search Term</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Count</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $results as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row->id ); ?></td>
                        <td><?php echo esc_html( $row->search_term ); ?></td>
                        <td><?php echo esc_html( $row->count ); ?></td>
                        <td><?php echo esc_html( $row->date ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}


