<?php

abstract class InstantPush_Init {

  public static function instantpush_menu() {
    add_menu_page(
        'InstantPush',              // page title
        'InstantPush',              // menu title
        'manage_options',           // capabilities
        'instantpush-top-menu',           // menu slug
        [self::class, 'instantpush_menu_html'], // content cb function
        'dashicons-admin-network'   // icon
    );
  }

  public static function instantpush_menu_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( isset( $_GET['settings-updated'] ) ) {
      add_settings_error( 'messages', 'message','Settings Saved', 'updated' );
    }

    settings_errors( 'messages' );
    ?>
    <div class="wrap">
      <h1><?php esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        settings_fields( 'instantpush_group' );
        do_settings_sections( 'instantpush-top-menu' );
        submit_button( 'Save Settings' );
        ?>
      </form>
    </div>
    <?php
  }

  public static function instantpush_settings() {

    register_setting( 'instantpush_group', 'instantpush_options' );

    add_settings_section(
      'instantpush_auth', // section slug
      'Settings', // title
      [self::class, 'instantpush_auth_section_html'], // content cb function
      'instantpush-top-menu' // page slug
    );

    add_settings_field(
      'instantpush_token', // field slug
      'Token :', // title
      [self::class, 'instantpush_token_html'], // content cb function
      'instantpush-top-menu', // page slug
      'instantpush_auth' // section slug
    );

    add_settings_section(
      'instantpush_notification_jobs', // section slug
      'Notification Jobs Settings', // title
      [self::class, 'instantpush_notification_jobs_html'], // content cb function
      'instantpush-top-menu' // page slug
    );

    add_settings_field(
      'instantpush_segment_id', // field slug
      'Segment ID :', // title
      [self::class, 'instantpush_segment_id_html'], // content cb function
      'instantpush-top-menu', // page slug
      'instantpush_notification_jobs' // section slug
    );

    add_settings_field(
     'instantpush_adspace_id', // field slug
     'Adspace ID :', // title
     [self::class, 'instantpush_adspace_id_html'], // content cb function
     'instantpush-top-menu', // page slug
     'instantpush_notification_jobs' // section slug
    );

    add_settings_field(
     'instantpush_notification_id', // field slug
     'Notification ID :', // title
     [self::class, 'instantpush_notification_id_html'], // content cb function
     'instantpush-top-menu', // page slug
     'instantpush_notification_jobs' // section slug
    );

  }

  public static function instantpush_auth_section_html() {
    ?>
    <p>
      Set InstantPush authorization token to access instantpush for sending notifications.
      <a href="https://instantpu.sh/publisher/account" target="_blank">Get your api token here</a>.
    </p>
    <?php
  }

  public static function instantpush_notification_jobs_html( $args ) {
   ?>
   <p>
     Notificaiton Jobs settings require adspace, segment and notification to be created on
     <a href="https://instantpu.sh/publisher/adspaces" target="_blank">InstantPush</a>.
     <br>
     <span>
       If you do not have an account with InstantPush, <a href="https://instantpu.sh/user/register" target="_blank">Register Now</a>.
     </span>
   </p>
   <?php
  }

  public static function instantpush_token_html( $args ) {
  	 $options = get_option( 'instantpush_options' );
  	 ?>
  	 <input type="password" class="regular-text code" name="instantpush_options[token]" value="<?php echo isset( $options['token'] ) ? esc_attr( $options['token'] ) : ''; ?>">
     <?php
  }

  public static function instantpush_segment_id_html( $args ) {
  	 $options = get_option( 'instantpush_options' );
  	 ?>
  	 <input type="number" class="small-text" step="1" min="1" name="instantpush_options[segment_id]" value="<?php echo isset( $options['segment_id'] ) ? esc_attr( $options['segment_id'] ) : ''; ?>">
     <?php
  }

  public static function instantpush_adspace_id_html( $args ) {
  	 $options = get_option( 'instantpush_options' );
  	 ?>
  	 <input type="number" class="small-text" step="1" min="1" name="instantpush_options[adspace_id]" value="<?php echo isset( $options['adspace_id'] ) ? esc_attr( $options['adspace_id'] ) : ''; ?>">
     <?php
  }

  public static function instantpush_notification_id_html( $args ) {
  	 $options = get_option( 'instantpush_options' );
  	 ?>
  	 <input type="number" class="small-text" step="1" min="1" name="instantpush_options[notification_id]" value="<?php echo isset( $options['notification_id'] ) ? esc_attr( $options['notification_id'] ) : ''; ?>">
     <?php
  }

}

add_action( 'admin_init', ['InstantPush_Init', 'instantpush_settings'] );
add_action('admin_menu', ['InstantPush_Init', 'instantpush_menu']);
