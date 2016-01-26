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

    $jobArgs = array(
      'keyword'     => ( ! empty( $instance['keyword'] ) ) ? $instance['keyword'] : false,
      'location'    => ( ! empty( $instance['location'] ) ) ? $instance['location'] : false,
      'geolocation' => ( ! empty( $instance['geolocation'] ) ) ? $instance['geolocation'] : false,
      'powered_by'  => ( ! empty( $instance['powered_by'] ) ) ? $instance['powered_by'] : true,
      'portals'     => ( ! empty( $instance['portals'] ) ) ?  $instance['portals'] : array(),
    );
    $jobs = jobify_get_jobs( $jobArgs );

    $rand    = time();

    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }

    if ( count( $jobs ) > 0 ) {
      shuffle( $jobs );
      $cnt = 0;
      echo '<div class="jobifyJobs"
        data-location="' . esc_attr( $jobArgs['location'] ) . '"
        data-geolocation="' . $jobArgs['geolocation'] . '"
        data-template="jobify-' . $rand . '"
        data-keyword="' . $jobArgs['keyword'] . '"
        data-apis="' . implode( '|', $jobArgs['portals'] ) . '"
        data-limit="' . $instance['limit'] . '"
      >';
      foreach ( $jobs as $key => $ary ) { $cnt++;
        if ( ! empty( $instance['limit'] ) && $cnt > $instance['limit'] ) break;
        if ( ! empty( $ary['error'] ) )
        {
          echo '<p>' . $ary['error'] . '</p>';
        }
        else
        {
          echo jobify_parse( $instance['template'], $ary );
        }
      }
      echo '</div>';

      if ( $instance['geolocation'] )
      {
        wp_enqueue_script( 'jobify-ajax' );
        echo '<div id="jobify-' . $rand . '" style="display: none !important;">' . $instance['template'] . '</div>';
      }

      if ( in_array( 'indeed', $jobArgs['portals'] ) )
      {
        wp_enqueue_script( 'jobify-indeed' );
        ?>
        <div class="jobify__indeed-attribution">
          <?php printf( __( '<span id=indeed_at><a href="%s">jobs</a> by <a
    href="%s" title="Job Search"><img
    src="%s" style="border: 0;
    vertical-align: middle;" alt="Indeed job search"></a></span>', 'jobify' ), 'http://www.indeed.com/', 'http://www.indeed.com/', 'http://www.indeed.com/p/jobsearch.gif' ); ?>
        </div>
        <?php
      }
    }
    else
    {
      echo '<p>' . __( 'No jobs available at this time.', 'jobify' ) . '</p>';
    }

    if ( $instance['powered_by'] )
    {
      ?>
      <div class="jobify__powered-by">
        <?php jobify_powered_by(); ?>
      </div>
      <?php
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

    $default_tpl = '<p><a href="[app_url]" target="_blank">[title]</a> ([company]) - [location]</p>';

    $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Jobs', 'jobify' );
    $template    = ! empty( $instance['template'] ) ? $instance['template'] : $default_tpl;
    $limit       = ! empty( $instance['limit'] ) ? $instance['limit'] : 5;
    $location    = ! empty( $instance['location'] ) ? $instance['location'] : '';
    $geolocation = isset( $instance['geolocation'] ) ? $instance['geolocation'] : 1;
    $keyword     = ! empty( $instance['keyword'] ) ? $instance['keyword'] : '';
    $powered_by  = isset( $instance['powered_by'] ) ? $instance['powered_by'] : 1;
    $portals     = isset( $instance['portals'] ) ? $instance['portals'] : array();
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'keyword' ); ?>"><?php _e( 'Keyword:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'keyword' ); ?>" name="<?php echo $this->get_field_name( 'keyword' ); ?>" type="text" value="<?php echo esc_attr( $keyword ); ?>">
    <span class="description"><?php _e( 'A search term, such as "ruby" or "java".', 'jobify' ); ?></span>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>">
    <span class="description"><?php _e( 'A city name, zip code, or other location search term.', 'jobify' ); ?></span>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'geolocation' ); ?>"><input id="<?php echo $this->get_field_id( 'geolocation' ); ?>" name="<?php echo $this->get_field_name( 'geolocation' ); ?>" type="checkbox"<?php if ( $geolocation ): ?> checked="checked"<?php endif; ?>> <?php _e( 'Enable geolocation' ); ?></label>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Job Template:' ); ?></label>
    <textarea rows="5" class="large-text code" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" type="text"><?php echo $template; ?></textarea>
    </p>
    <h4><?php _e( 'Available template shortcodes' ); ?>:</h4>
    <ul>
      <li><code>[app_url]</code> - <?php _e( 'Job application URL' ); ?>
      <li><code>[title]</code> - <?php _e( 'Job title' ); ?>
      <li><code>[company]</code> - <?php _e( 'Company' ); ?>
      <li><code>[description]</code> - <?php _e( 'Job description' ); ?>
      <li><code>[location]</code> - <?php _e( 'Job location' ); ?>
    </ul>

    <h4><?php _e( 'Available APIs:' ); ?></h4>
    <?
    $portals = ! empty( $instance['portals'] ) ? $instance['portals'] : array();
    foreach ( $jobifyAPIs as $key => $ary )
    {
      ?>
      <div class="jobify__api">
        <div class="jobify__api__row">
          <div class="jobify__api__half">
            <img src="<?php echo  $ary['logo']; ?>" alt="<?php echo $ary['title']; ?>">
          </div>
          <div class="jobify__api__half" style="line-height: 50px;">
            <label><input type="checkbox" name="<?php echo $this->get_field_name( 'portals'); ?>[]" id="<?php echo $this->get_field_id( 'portals' ); ?>" value="<?php echo esc_attr( $ary['key'] ); ?>"<?php if ( in_array( $ary['key'], $portals ) ): ?> checked="checked"<?php endif; ?>> <?php _e( 'Enable' ); ?> <?php echo $ary['title']; ?></label>
          </div>
        </div>
        <?php if ( in_array( $ary['key'], $portals ) ): ?>
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
          endif; endif; ?>
          <?php if ( ! empty( $ary['desc'] ) ) { echo '<p class="description" style="margin: 1em 0 0 0">'. $ary['desc'] . '</p>'; } ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?
    }
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'powered_by' ); ?>"><input id="<?php echo $this->get_field_id( 'powered_by' ); ?>" name="<?php echo $this->get_field_name( 'powered_by' ); ?>" type="checkbox"<?php if ( $powered_by ): ?> checked="checked"<?php endif; ?>> <?php _e( 'Show powered by Jobify link' ); ?></label>
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

    $instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['template']    = ( ! empty( $new_instance['template'] ) ) ? $new_instance['template'] : '';
    $instance['limit']       = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';
    $instance['location']    = ( ! empty( $new_instance['location'] ) ) ? strip_tags( $new_instance['location'] ) : '';
    $instance['keyword']     = ( ! empty( $new_instance['keyword'] ) ) ? strip_tags( $new_instance['keyword'] ) : '';
    $instance['geolocation'] = ( ! empty( $new_instance['geolocation'] ) ) ? strip_tags( $new_instance['geolocation'] ) : '';
    $instance['powered_by']  = ( ! empty( $new_instance['powered_by'] ) ) ? strip_tags( $new_instance['powered_by'] ) : '';
    $instance['portals']     = ( ! empty( $new_instance['portals'] ) ) ? $new_instance['portals'] : array();

    foreach ( $jobifyAPIs as $key => $ary )
    {
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