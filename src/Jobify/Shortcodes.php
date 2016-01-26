<?php
class Jobify_Shortcodes {
  public function run()
  {
    add_action( 'init', function()
    {
      add_shortcode( 'jobify', array( $this, 'jobify' ) );
      add_shortcode( 'indeed-jobroll', array( $this, 'indeed_jobroll' ) );
      add_shortcode( 'indeed-job-search', array( $this, 'indeed_job_search' ) );
    });
  }

  public function jobify( $atts, $content = null )
  {
    $args = array(
      'keyword'     => ( ! empty( $atts['keyword'] ) ) ? $atts['keyword'] : false,
      'location'    => ( ! empty( $atts['location'] ) ) ? $atts['location'] : false,
      'geolocation' => ( ! empty( $atts['geolocation'] ) ) ? $atts['geolocation'] : false,
      'powered_by'  => ( ! empty( $atts['powered_by'] ) ) ? $atts['powered_by'] : true,
      'powered_by'  => ( ! empty( $atts['powered_by'] ) ) ? $atts['powered_by'] : true,
      'portals'     => ( ! empty( $atts['portals'] ) ) ? explode( "|", $atts['portals'] ) : array(),
    );
    $jobs = jobify_get_jobs( $args );

    ob_start();
    if ( count( $jobs ) > 0 )
    {
      foreach( $jobs as $key => $ary )
      {
        echo jobify_parse( html_entity_decode( $content ), $ary );
      }

      if ( in_array( 'indeed', $args['portals'] ) )
      {
        echo  '<div class="jobify__indeed-attribution">' . sprintf( __( '<span id=indeed_at><a href="%s">jobs</a> by <a
    href="%s" title="Job Search"><img
    src="%s" style="border: 0;
    vertical-align: middle;" alt="Indeed job search"></a></span>', 'jobify' ), 'http://www.indeed.com/', 'http://www.indeed.com/', 'http://www.indeed.com/p/jobsearch.gif' ) . '</div>';
      }

      if ( $args['powered_by'] )
      { ?>
      <div class="jobify__powered-by">
        <?php jobify_powered_by(); ?>
      </div>
      <?php }
    }
    return ob_get_clean();
  }



  public function indeed_job_search( $atts )
  {
    $settings     = jobify_settings();
    $publisher_id = ( ! empty( $settings['publisher_id'] ) ) ? $settings['publisher_id'] : '9769494768160125';
    $type         = ( ! empty( $atts['type'] ) ) ? $atts['type'] : 'all-in-one';
    ob_start();
    switch ( $type )
    {
      case 'all-in-one':
        ?>
<form action='http://www.indeed.com/jobs' METHOD='GET'>
    <input type="hidden" name="indpubnum" value="<?php echo $publisher_id; ?>">

    <table cellspacing='0' style='font-family:arial'>
        <tr><td style='font-size:16px;color:#F60'><b><?php echo _e( 'Job Search', 'jobify' ); ?></b></td><td> </td></tr>
        <tr>
            <td><input name='q' value='' size='25'></td>
            <td><input type='submit' value='Find Jobs'/></td>
        </tr>
        <tr>
            <td valign='top' style='font-size:10px'>job title, keywords, company, location</td>
            <td colspan='1' valign='top' style='font-size:13px;'>
                <span id=indeed_at>
                    <a href="http://www.indeed.com/?indpubnum=<?php echo $publisher_id; ?>" style="text-decoration:none; color: #000">jobs by</a>
                    <a href="http://www.indeed.com/?indpubnum=<?php echo $publisher_id; ?>" title=Job Search>
                        <img src="http://www.indeed.com/p/jobsearch.gif" style="border: 0;vertical-align: middle;" alt="job search">
                    </a>
                </span>
            </td>
        </tr>
    </table>
</form>
        <?php
        break;
    }
    return ob_get_clean();
  }

  public function indeed_jobroll( $atts )
  {
    $element_id   = 'indeedjobroll' . time();
    $publisher_id = ( ! empty( $atts['publisher_id'] ) ) ? $atts['publisher_id'] : '9769494768160125';
    $keyword      = ( ! empty( $atts['keyword'] ) ) ? $atts['keyword'] : '';
    $location     = ( ! empty( $atts['location'] ) ) ? $atts['location'] : '';
    $title        = ( ! empty( $atts['title'] ) ) ? $atts['title'] : 'Jobs from Indeed';
    $background   = ( ! empty( $atts['background'] ) ) ? $atts['background'] : '#fff';
    $size         = ( ! empty( $atts['size'] ) ) ? $atts['size'] : '300x250';
    $border_color = ( ! empty( $atts['border_color'] ) ) ? $atts['border_color'] : '#ddd';
    $header_color = ( ! empty( $atts['header_color'] ) ) ? $atts['header_color'] : '#000';
    $text_color   = ( ! empty( $atts['text_color'] ) ) ? $atts['text_color'] : '#000';
    $link_color   = ( ! empty( $atts['link_color'] ) ) ? $atts['link_color'] : '#00c';
    $channel      = ( ! empty( $atts['channel'] ) ) ? $atts['channel'] : '';
    $pagination   = ( ! empty( $atts['pagination'] ) ) ? $atts['pagination'] : false;
    $pages        = ( ! empty( $atts['pages'] ) ) ? $atts['pages'] : 0;

    switch ( $size )
    {
      case '180x150':
        $width  = '180px';
        $height = '150px';
        $limit  = 3;
        $t      = 20;
        $c      = 15;
        break;
      case '300x250':
        $width  = '300px';
        $height = '250px';
        $limit  = 4;
        $t      = 40;
        $c      = 30;
        break;
      case '160x600':
        $width  = '160px';
        $height = '600px';
        $limit  = 10;
        $t      = 20;
        $c      = 10;
        break;
      case '300x600':
        $width  = '300px';
        $height = '600px';
        $limit  = 10;
        $t      = 40;
        $c      = 30;
        break;
      case '728x90':
        $width  = '728px';
        $height = '90px';
        $limit  = 5;
        $t      = 12;
        $c      = 10;
        break;
    }

    ob_start();
    ?>

<style>
#<?php echo $element_id; ?>{padding-bottom: 5px;}#<?php echo $element_id; ?>
.company_location{font-size: 11px;overflow: hidden;display:block;}
#<?php echo $element_id; ?>.wide .job{display:block;float:left;margin-right: 5px;width: 135px;overflow: hidden}
#indeed_widget_wrapper{position: relative;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 18px;padding: 10px;height: auto;overflow: hidden;}
#indeed_widget_header{font-size:18px; padding-bottom: 5px; }
#indeed_search_wrapper{clear: both;font-size: 12px;margin-top: 0px;padding-top: 2px;}
#indeed_search_wrapper label{font-size: 12px;line-height: inherit;text-align: left; margin-right: 5px;}
#indeed_search_wrapper input[type='text']{width: 100px; font-size: 11px; }
#indeed_search_wrapper #qc{float:left;}
#indeed_search_wrapper #lc{float:right;}
#indeed_search_wrapper.stacked #qc, #indeed_search_wrapper.stacked #lc{float: none; clear: both;}
#indeed_search_wrapper.stacked input[type='text']{width: 150px;}
#indeed_search_wrapper.stacked label{display: block;padding-bottom: 5px;}
#indeed_search_footer{width:295px; padding-top: 5px; clear: both;}
#indeed_link{position: absolute;bottom: 1px;right: 5px;clear: both;font-size: 11px; }
#indeed_link a{text-decoration: none;}
#results .job{padding: 1px 0px;}
#pagination { clear: both; }

#indeed_widget_wrapper{ width: <?php echo $width; ?>; height: <?php echo $height; ?>; background: <?php echo $background; ?>}
#indeed_widget_wrapper{ border: 1px solid <?php echo $border_color; ?> }
#indeed_widget_wrapper, #indeed_link a{ color: <?php echo $text_color; ?>; }
#<?php echo $element_id; ?>, #indeed_search_wrapper{ border-top: 1px solid <?php echo $border_color; ?>; }
#<?php echo $element_id; ?> a { color: <?php echo $link_color; ?> }
#indeed_widget_header{ color: <?php echo $header_color; ?>; }
</style>

<script type='text/javascript'>
var ind_pub = '<?php echo $publisher_id; ?>';
var ind_el = '<?php echo $element_id; ?>';
var ind_pf = '';
var ind_q = '<?php echo $keyword; ?>';
var ind_l = '<?php echo $location; ?>';
var ind_chnl = '<?php echo $channel; ?>';
var ind_n = <?php echo $limit; ?>;
var ind_d = 'http://www.indeed.com';
var ind_t = 40;
var ind_c = 30;
<?php if ( $pagination ): ?>
var ind_pgn = 1;
var ind_pgnCnt = <?php echo $pages; ?>;
<?php endif; ?>
</script>

<script src='http://www.indeed.com/ads/jobroll-widget-v3.js'></script>
<div id='indeed_widget_wrapper'>
  <?php if ( ! empty( $title ) ): ?><div id='indeed_widget_header'><?php _e( $title, 'jobify' ); ?></div><?php endif; ?>
  <div id='<?php echo $element_id; ?>'></div>
  <div id='indeed_search_wrapper'>
    <script>
    function clearDefaults() {
      var formInputs = document.getElementById('indeed_jobform').elements;
      for(var i = 0; i < formInputs.length; i++) {
        if(formInputs[i].value == 'title, keywords' || formInputs[i].value == 'city, state, or zip') {
          formInputs[i].value = '';
        }
      }
    }
    </script>
    <form onsubmit='clearDefaults();' method='get' action='http://www.indeed.com/jobs' id='indeed_jobform' target="_new">
    <div id="qc"><label><?php _e( 'What', 'jobify' ); ?>:</label><input type='text' onfocus='this.value=""' value='title, keywords' name='q' id='q'></div>
    <div id="lc"><label><?php _e( 'Where', 'jobify' ); ?>:</label><input type='text' onfocus='this.value=""' value='city, state, or zip' name='l' id='l'></div>
    <div id='indeed_search_footer'>
      <div style='float:left'><input type='submit' value='Find Jobs' class='findjobs'></div>
    </div>
    <input type='hidden' name='indpubnum' id='indpubnum' value='<?php echo $publisher_id; ?>'>
    </form>
    <div id='indeed_link'>
      <a title="Job Search" href="http://www.indeed.com/?indpubnum=<?php echo $publisher_id; ?>" target="_new">jobs by <img alt=Indeed src='http://www.indeed.com/p/jobsearch.gif' style='border: 0;vertical-align: bottom;'></a>
    </div>
  </div>
</div>
    <?php
    return ob_get_clean();
  }
}