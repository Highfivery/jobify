<?php
class Jobify_Shortcodes {
  public function run()
  {
    add_action( 'init', function()
    {
      add_shortcode( 'indeed-jobroll', array( $this, 'indeed_jobroll' ) );
    });
  }

  public function indeed_jobroll( $atts )
  {
    $element_id   = 'indeedjobroll' . time();
    $publisher_id = ( ! empty( $atts['publisher_id'] ) ) ? $atts['publisher_id'] : '9769494768160125';
    $keyword      = ( ! empty( $atts['keyword'] ) ) ? $atts['keyword'] : '';
    $location     = ( ! empty( $atts['location'] ) ) ? $atts['location'] : '';
    $title        = ( ! empty( $atts['title'] ) ) ? $atts['title'] : 'Jobs from Indeed';
    $background   = ( ! empty( $atts['background'] ) ) ? $atts['background'] : '#fff';
    $width        = ( ! empty( $atts['width'] ) ) ? $atts['width'] : '180px';
    $height       = ( ! empty( $atts['height'] ) ) ? $atts['height'] : '150px';
    $border_color = ( ! empty( $atts['border_color'] ) ) ? $atts['border_color'] : '#ddd';
    $header_color = ( ! empty( $atts['header_color'] ) ) ? $atts['header_color'] : '#000';
    $text_color   = ( ! empty( $atts['text_color'] ) ) ? $atts['text_color'] : '#000';
    $link_color   = ( ! empty( $atts['link_color'] ) ) ? $atts['link_color'] : '#00c';

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
var ind_chnl = '';
var ind_n = 4;
var ind_d = 'http://www.indeed.com';
var ind_t = 40;
var ind_c = 30;
</script>
<script src='http://www.indeed.com/ads/jobroll-widget-v3.js'></script>
<div id='indeed_widget_wrapper'>
  <?php if ( ! empty( $title ) ): ?><div id='indeed_widget_header'><?php _e( $title, 'jobify' ); ?></div><?php endif; ?>
  <div id='<?php echo $element_id; ?>'></div>
  <div id='indeed_link'>
    <a title="Job Search" href="http://www.indeed.com/" target="_new">jobs by <img alt=Indeed src='http://www.indeed.com/p/jobsearch.gif' style='border: 0;vertical-align: bottom;'></a>
  </div>
</div>
    <?php
    return ob_get_clean();
  }
}