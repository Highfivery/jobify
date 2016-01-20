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
    global $jobifyAPIs;

    $jobs = array();

    foreach ( $jobifyAPIs as $key => $ary )
    {
      $enabled = ! empty( $instance[$ary['name']] ) ? $instance[$ary['name']] : FALSE;

      if ( $enabled )
      {
        $jobs = array_merge( $jobs, $ary['getJobs']( $instance ) );
      }
    }

    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }

    if ( count( $jobs ) > 0 ) {
      shuffle( $jobs );
      $cnt = 0;
      foreach ( $jobs as $key => $ary ) { $cnt++;
        if ( ! empty( $instance['limit'] ) && $cnt > $instance['limit'] ) break;
        echo '<p><a href="' . $ary['url'] . '" target="_blank">' . $ary['title']. '</a> - ' . $ary['location'] . '</p>';
      }
    }
    else
    {
      echo '<p>' . __( 'No jobs available at this time.', 'jobify' ) . '</p>';
    }

    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    global $jobifyAPIs;

    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Jobs', 'jobify' );
    $limit = ! empty( $instance['limit'] ) ? $instance['limit'] : '';
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?

    foreach ( $jobifyAPIs as $key => $ary )
    {
      $enabled = ! empty( $instance[$ary['name']] ) ? $instance[$ary['name']] : FALSE;
      ?>
      <div class="jobify__api">
        <div class="jobify__api__row">
          <div class="jobify__api__half">
            <img src="<?php echo  $ary['logo']; ?>" alt="<?php echo $ary['title']; ?>">
          </div>
          <div class="jobify__api__half" style="line-height: 50px;">
            <label><input type="checkbox" name="<?php echo $this->get_field_name( $ary['name'] ); ?>" id="<?php echo $this->get_field_id( $ary['name'] ); ?>" value="1"<?php if ( $enabled ): ?> checked="checked"<?php endif; ?>> <?php _e( 'Enable' ); ?> <?php echo $ary['title']; ?></label>
          </div>
        </div>
        <?php if ( $enabled ): ?>
          <?php foreach ( $ary['options'] as $k => $option ):
          if ( isset( $option['group'] ) &&  is_array( $option['group'] ) ):
            ?>
            <div class="jobify__api__row">
              <?php foreach ( $option['group'] as $i => $g ):
              $value = ! empty( $instance[$g['name']] ) ? $instance[$g['name']] : $g['default']; ?>
              <div class="jobify__api__half">
                <label for="<?php echo $this->get_field_id( $g['name'] ); ?>"><?php echo $g['title']; ?></label>
                <?php if ( isset( $g['type'] ) && $g['type'] === "select" ): ?>
                <select class="widefat" id="<?php echo $this->get_field_id( $g['name'] ); ?>" name="<?php echo $this->get_field_name( $g['name'] ); ?>">
                  <?php foreach( $g['options'] as $x => $t ): ?>
                  <option value="<?php echo $x; ?>"<?php if ( $value === $x ): ?>selected="selected"<?php endif; ?>><?php echo $t; ?></option>
                  <?php endforeach; ?>
                </select>
                <?php elseif (isset( $g['type'] ) && $g['type'] === "number" ): ?>
                  <input class="widefat" id="<?php echo $this->get_field_id( $g['name'] ); ?>" name="<?php echo $this->get_field_name( $g['name'] ); ?>" type="number" value="<?php echo esc_attr( $value ); ?>">
                <?php else: ?>
                  <input class="widefat" id="<?php echo $this->get_field_id( $g['name'] ); ?>" name="<?php echo $this->get_field_name( $g['name'] ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
                <?php endif; ?>
                <span class="description"><?php echo $g['desc']; ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            <?php if ( ! empty( $option['desc'] ) ): ?>
              <p class="description" style="margin-top: .5em;"><?php echo $option['desc']; ?></p>
            <?php endif; ?>
        <p>
            <?
          else:
          $value = ! empty( $instance[$option['name']] ) ? $instance[$option['name']] : $option['default'];
          if ( isset( $option['type'] ) && $option['type'] === "checkbox" ): ?>
            <label for="<?php echo $this->get_field_id( $option['name'] ); ?>"><?php echo $option['title']; ?></label><br>
            <?php foreach( $option['options'] as $i => $v ): ?>
            <label><input type="checkbox" name="<?php echo $this->get_field_name( $option['name'] ); ?>[]" id="<?php echo $this->get_field_id( $option['name'] ); ?>-<?php echo $i; ?>" value="<?php echo $i; ?>"<?php if ( is_array( $value ) && in_array( $i, $value ) ): ?> checked="checked"<?php endif; ?>> <?php echo $v; ?></label>
            <?php endforeach; ?>
            <span class="description"><?php echo $option['desc']; ?></span>
          <?php else: ?>
          <p>
          <label for="<?php echo $this->get_field_id( $option['name'] ); ?>"><?php echo $option['title']; ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id( $option['name'] ); ?>" name="<?php echo $this->get_field_name( $option['name'] ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
          <span class="description"><?php echo $option['desc']; ?></span>
          </p>
          <?php
          endif; endif;
          endforeach; ?>
        <?php endif; ?>
      </div>
      <?
    }
    ?>
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
    global $jobifyAPIs;

    $instance = array();

    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

    foreach ( $jobifyAPIs as $key => $ary )
    {
      $instance[$ary['name']] = ( ! empty( $new_instance[$ary['name']] ) ) ? strip_tags( $new_instance[$ary['name']] ) : $ary['default'];

      foreach ( $ary['options'] as $k => $option )
      {
        if ( isset( $option['group'] ) &&  is_array( $option['group'] ) )
        {
          foreach ( $option['group'] as $i => $g )
          {
              $instance[$g['name']] = ( ! empty( $new_instance[$g['name']] ) ) ? strip_tags( $new_instance[$g['name']] ) : $g['default'];
          }
        }
        else
        {
          if ( isset( $option['type'] ) && $option['type'] === "checkbox" )
          {
            $instance[$option['name']] = ( ! empty( $new_instance[$option['name']] ) ) ? $new_instance[$option['name']] : $option['default'];
          }
          else
          {
            $instance[$option['name']] = ( ! empty( $new_instance[$option['name']] ) ) ? strip_tags( $new_instance[$option['name']] ) : $option['default'];
          }
        }
      }
    }

    return $instance;
  }
}