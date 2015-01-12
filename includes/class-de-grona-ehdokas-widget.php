<?php

if ( ! class_exists( 'De_Grona_Ehdokas_CTA_Widget' ) ) {
  class De_Grona_Ehdokas_CTA_Widget extends WP_Widget {

    /**
     * The main plugin object.
     * @var   object
     * @access  public
     * @since   0.1.0
     */
    public $parent = null;

    public function __construct ( ) {
      $this->parent = De_Grona_Ehdokas::instance();
      $widget_ops = array( 'classname' => 'degrona_candidate_cta_buttons_widget', 'description' => __( 'Display your call to action buttons', PLUGIN_TEXT_DOMAIN ) );
      parent::__construct( 'degrona_ehdokas_cta_widget', __( 'Call To Action Buttons', PLUGIN_TEXT_DOMAIN ), $widget_ops );

    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
      if ( $instance ) {
        $title = $instance['title'];
      }
      else {
        $title = '';
      }
  ?>

      <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' , PLUGIN_TEXT_DOMAIN ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
      </p>

  <?php
    }
    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
      $instance['title'] = strip_tags( $new_instance['title'] );
      return $instance;
    }
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
      echo $args['before_widget'];
      if ( ! empty( $instance['title'] ) ) {
        echo $args['before_title'];
        echo esc_html( $instance['title'] );
        echo $args['after_title'];
      }
  ?>
    <?php
      echo $this->parent->get_call_to_action_buttons();
    ?>
  <?php
      echo $args['after_widget'];
    }

  }

}

function degrona_ehdokas_register_widgets() {
  register_widget( 'De_Grona_Ehdokas_CTA_Widget' );
}

add_action( 'widgets_init', 'degrona_ehdokas_register_widgets' );