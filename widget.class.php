<?php

/**
 * Recent_Events widget class
 * 
 * 
 */


class PELP_Widget_Recent_Events extends WP_Widget {

    /**
     * Sets up a new Recent Posts widget instance.
     *
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'widget_recent_events',
            'description'                 => __( 'Your site&#8217;s most recent Events.' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'recent-events', __( 'Recent Events' ), $widget_ops );
        $this->alt_option_name = 'widget_recent_events';
    }
    
    /**
     * Outputs the content for the current Recent Posts widget instance.
     *
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
    
        /**
         * Filters the arguments for the Recent Posts widget.
         *
         */
        $r = new WP_Query(
            apply_filters(
                'widget_posts_args',
                array(
                    'posts_per_page'      => $number,
                    'no_found_rows'       => true,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true, 
                    'post_type'         => 'UB_single_event',
                    'order'     => 'DESC',
                    'orderby'   => 'meta_value', 
                    'meta_query' => array(
                                        array('key' => 'event_date',
                                        )
                                    ) 
                ),
                $instance
            )
        );

        if ( ! $r->have_posts() ) {
            return;
        }

        /**
         * Echo the post type in the loop
         *
         */
        ?>
        <?php echo $args['before_widget']; ?>
        <?php
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        ?>
        <ul>
        <?php while ( $r->have_posts() ) : $r->the_post(); ?>
            <?php
            $post_title    = get_the_title( $recent_post->ID );
            $title         = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
            $post_content  = get_the_content( $recent_post->ID );
            $custom        = get_post_custom($recent_post->ID);
            $location      = isset($custom["location"][0])?$custom["location"][0]:'';
            $date          = isset($custom["event_date"][0])?$custom["event_date"][0]:'';

            ?>
            <li> 
                <div><b><?php echo $date; ?></b></div>
                <div><h5><?php echo $title; ?></h5>
                <div><i><?php echo $location; ?></i></div>
                <div><?php echo $post_content ?></div>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php
        echo $args['after_widget'];
    }


    
    /**
     * Handles updating the settings for the current Recent Posts widget instance.
     *
     */
    public function update( $new_instance, $old_instance ) {
        $instance              = $old_instance;
        $instance['title']     = sanitize_text_field( $new_instance['title'] );
        $instance['number']    = (int) $new_instance['number'];
        return $instance;
    }


    
    /**
     * Outputs the settings form for the Recent Posts widget.
     *
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
    
        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
        <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>
        <?php
    }
}