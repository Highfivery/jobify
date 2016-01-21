<?php
/**
 * General settings.
 *
 * @since 1.1.0
 */

/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<form method="post" action="<?php echo $action; ?>">
  <?php settings_fields( $tab ); ?>
  <?php do_settings_sections( $tab ); ?>
  <?php submit_button(); ?>
</form>
