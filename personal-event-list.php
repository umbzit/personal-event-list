<?php 
/**
 * Plugin Name:       Personal Event List Widget
 * Plugin URI:        https://www.cybstudio.com/
 * Description:       A list of personal events with a custom title, a custom location, a datepicker and a custom description.
 * Version:           0.1
 * Author:            Umberto De Palma
 * Author URI:        https://www.cybstudio.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 */


require_once dirname(__FILE__) . '/widget.class.php';

/**
* Create class
*
*/
class UB_Personal_Events_List_Plugin {

  // Constructor
  public function __construct() {
      add_action('init', array($this, 'register_post_type'));
      add_action('widgets_init', array($this, 'register_widgets'));
      add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
      add_action('save_post', array($this, 'save_events_details'));
  }

  
/**
* Register the post type
*
*/
  public function register_post_type(){

    register_post_type('UB_single_event',array(
      'labels'=> array(
        'name' => __('Events'),
        'singular_name' => __('Event'),
        'add_new' => __( 'Add New' ),
        'add_new_item' =>  __( 'Add New Event'),
        'all_items' => __( 'All Events'),
        'edit_item' => __( 'Edit Event'),
        'new_item' =>  __( 'New Event'),
        'view_item' =>  __( 'View Event'),
        'search_items' => __( 'Search Events'),
        'not_found' =>  __( 'No Events Found'),
        'not_found_in_trash' => __( 'No Events Found in Trash')
      ),
      'description' => __('Personal Events List'),
      'supports' => array(
        'title','editor'
      ),
      'public' => TRUE,
      'show_in_menu'  =>   TRUE,
      'query_var'     =>   true,
      'publicly_queryable'    => true,
      'capability_type'       => 'post',
      'has_archive'   =>   TRUE,
      'menu_icon' => 'dashicons-calendar-alt',
      'rewrite' => array(
        'slug' => __('events'),
    ),
    ));

    flush_rewrite_rules();

  }

  /**
   *  Register the custom widget for this plugin
   * see PELP_Widget_Recent_Events in widget.class.php
   */

  public function register_widgets(){
    register_widget('PELP_Widget_Recent_Events');
  }


   /**
   *  Add custom metabox
   */
  public function add_custom_meta_box()
  {
      add_meta_box(
      'custom-meta-box',
      __( 'Location' ),
      array($this, 'custom_meta_box_markup'),
      'UB_single_event');

      add_meta_box(
        'dates-meta-box',
        __( 'Event date' ),
        array($this, 'dates_meta_box_markup'),
        'UB_single_event',
        'side',
        'high');
  }

  public function custom_meta_box_markup( $post) {

    global $post;
    $custom = get_post_custom($post->ID);
    $location = isset($custom["location"][0])?$custom["location"][0]:'';
    ?>
    <label>Event location:</label> <input name="location" value="<?php echo $location; ?>">
    <?php
  }


  public function dates_meta_box_markup()
  {
      wp_nonce_field(basename(__FILE__), "meta-box-nonce");
      $custom = get_post_custom($post->ID);
      $date = isset($custom["event_date"][0])?$custom["event_date"][0]:'';
      ?>
          <div>
              <label for="event_date"><?php echo __( 'Add the date of the event' ) ?></label>
              <input name="event_date" type="date" value="<?php echo ($date); ?>">
          </div>
      <?php  
  }

 

  /**
   *  Update post meta
   */
  public function save_events_details()
  {
      global $post;
      update_post_meta($post->ID, "location", $_POST["location"]);
      update_post_meta( $post->ID, 'event_date', $_POST[ 'event_date' ] );
  }  

}

  /**
   *  Instantiate
   */
$UB_Personal_Events_List_Plugin = new UB_Personal_Events_List_Plugin();


