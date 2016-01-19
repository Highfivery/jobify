<?php
namespace JobsWidget;

class JobsWidget extends \WP_Widget {
  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'jobify_widget',
      __( 'Jobify', 'jobify' ),
      array( 'description' => __( 'Displays a list of job postings.', 'jobify' ), ) );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    $jobs = $this->get_github_jobs($instance);

    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }

    if ( count( $jobs ) > 0 ) {
      shuffle( $jobs );
      $cnt = 0;
      foreach ( $jobs as $key => $obj ) { $cnt++;
        if ( ! empty( $instance['limit'] ) && $cnt > $instance['limit'] ) break;
        echo '<p><a href="' . $obj->url . '" target="_blank">' . $obj->title. '</a> - ' . $obj->location . '</p>';
      }
    }
    else
    {
      echo '<p>' . __( 'No jobs available at this time.', 'jobify' ) . '</p>';
    }

    echo $args['after_widget'];
  }

  public function get_github_jobs( $options ) {
    $link = 'https://jobs.github.com/positions.json?';

    if ( ! empty( $options['keyword'] ) ) {
      $link .= 'description=' . urlencode( $options['keyword'] ) . '&';
    }

    if ( ! empty( $options['location'] ) ) {
      $link .= 'location=' . urlencode( $options['location'] ) . '&';
    }

    if ( ! empty( $options['full_time'] ) ) {
      $link .= 'full_time=' . urlencode( $options['full_time'] );
    }

    return json_decode( file_get_contents( $link ) );
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Jobs', 'jobify' );
    $apis  = ! empty( $instance['apis'] ) ? $instance['apis'] : FALSE;

    $keyword  = ! empty( $instance['keyword'] ) ? $instance['keyword'] : '';
    $location = ! empty( $instance['location'] ) ? $instance['location'] : '';
    $limit    = ! empty( $instance['limit'] ) ? $instance['limit'] : __( 'Limit', 'jobsapi' );
    $fulltime = ! empty( $instance['fulltime'] ) ? $instance['fulltime'] : ''
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
     <p>
    <label for="<?php echo $this->get_field_id( 'apis' ); ?>"><?php _e( 'APIs:' ); ?></label><br>
    <label><input type="checkbox" name="<?php echo $this->get_field_name( 'apis' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo esc_attr( 'GitHub' ); ?>"> <?php _e( 'GitHub Jobs' ); ?></label>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'keyword' ); ?>"><?php _e( 'Keyword:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'keyword' ); ?>" name="<?php echo $this->get_field_name( 'keyword' ); ?>" type="text" value="<?php echo esc_attr( $keyword ); ?>">
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>">
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'fulltime' ); ?>"><?php _e( 'Full-time:' ); ?></label><br>
    <label><input type="radio" name="<?php echo $this->get_field_name( 'fulltime' ); ?>-yes" id="<?php echo $this->get_field_id( 'fulltime' ); ?>-yes" value="1"> <?php _e( 'Yes' ); ?></label>
    <label><input type="radio" name="<?php echo $this->get_field_name( 'fulltime' ); ?>-no" id="<?php echo $this->get_field_id( 'fulltime' ); ?>-no" value="0"> <?php _e( 'No' ); ?></label>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
    </p>
    <?php
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['apis']     = ( ! empty( $new_instance['apis'] ) ) ? strip_tags( $new_instance['apis'] ) : '';
    $instance['keyword']  = ( ! empty( $new_instance['keyword'] ) ) ? strip_tags( $new_instance['keyword'] ) : '';
    $instance['location'] = ( ! empty( $new_instance['location'] ) ) ? strip_tags( $new_instance['location'] ) : '';
    $instance['fulltime'] = ( ! empty( $new_instance['fulltime'] ) ) ? strip_tags( $new_instance['fulltime'] ) : '';
    $instance['limit']    = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

    return $instance;
  }
}