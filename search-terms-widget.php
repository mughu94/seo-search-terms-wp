<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_STT_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'seo_stt_widget',
            esc_html__( 'SEO Search Terms', 'text_domain' ),
            array( 'description' => esc_html__( 'Displays search terms', 'text_domain' ), )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_stt_terms';
        $limit = ! empty( $instance['limit'] ) ? intval( $instance['limit'] ) : 5;
        $order = ! empty( $instance['order'] ) ? $instance['order'] : 'random';

        if ( $order == 'top' ) {
            $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY count DESC LIMIT $limit" );
        } else {
            $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY RAND() LIMIT $limit" );
        }

        if ( ! empty( $results ) ) {
            echo '<ul>';
            foreach ( $results as $row ) {
                $search_url = home_url( '/?s=' . urlencode( $row->search_term ) );
                echo '<li><a href="' . esc_url( $search_url ) . '">' . esc_html( $row->search_term ) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No search terms found.</p>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Search Terms', 'text_domain' );
        $limit = ! empty( $instance['limit'] ) ? $instance['limit'] : 5;
        $order = ! empty( $instance['order'] ) ? $instance['order'] : 'random';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_attr_e( 'Number of terms to show:', 'text_domain' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_attr_e( 'Order:', 'text_domain' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
                <option value="random" <?php selected( $order, 'random' ); ?>>Random</option>
                <option value="top" <?php selected( $order, 'top' ); ?>>Top</option>
            </select>
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? intval( $new_instance['limit'] ) : 5;
        $instance['order'] = ( ! empty( $new_instance['order'] ) ) ? sanitize_text_field( $new_instance['order'] ) : 'random';
        return $instance;
    }
}

function register_seo_stt_widget() {
    register_widget( 'SEO_STT_Widget' );
}
add_action( 'widgets_init', 'register_seo_stt_widget' );
