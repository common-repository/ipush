<?php
abstract class InstantPush_Send_Notification {

    public static function add() {
      $settings = get_option('instantpush_options');
      if (!isset($settings['notification_id']) || empty(trim($settings['notification_id'])) || !isset($settings['token'])) {
        return;
      }
      $screens = ['post', 'instantpush_cpt'];
      foreach ($screens as $screen) {
        add_meta_box(
          'instantpush_box_id',   // Unique ID
          'InstantPush',          // Box title
          [self::class, 'html'],  // Content callback, must be of type callable
          $screen,                // Post type
          'side',
          'high'
        );
      }
    }

    public static function save($post_id) {
      if (array_key_exists('instantpush_field', $_POST)) {
        self::create_notification_job($post_id);
        update_post_meta(
          $post_id,
          'instantpush_meta_key',
          1
        );
      } else {
        delete_post_meta($post_id, 'instantpush_meta_key');
      }
    }

    public static function html($post) {
      $value = get_post_meta($post->ID, 'instantpush_meta_key', true);
      ?>
      <input id="instantpush_field" type="checkbox" name="instantpush_field" value="<?php echo $value; ?>" <?php if($value) { echo 'checked'; } ?>>
      <label for="instantpush_field">Send InstantPush Notification</label>
      <?php
    }

    public static function create_notification_job( $ID ) {

    	$settings = get_option('instantpush_options');

      if (isset($settings['notification_id']) && isset($settings['token'])) {
        $post = get_post($ID);
      	$post_url = get_permalink( $post->ID );
      	$post_title = substr($post->post_title,0,100);
        if (has_excerpt( $post->ID )) {
          $post_body = substr($post->post_excerpt,0,300);
        } else {
          $post_body = substr(wp_trim_excerpt('',$post),0,300);
        }

      	$macros =  array(
    			"title" => $post_title,
    			"body" 	=> $post_body,
    			"click_url" => $post_url
      	);

        if ( has_post_thumbnail( $post->ID ) ) {
          $macros['image_url'] = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) )[0];
        }

        $api_url = "https://api.instantpu.sh/api/notification_jobs/create";
        $data_array =  array(
          "body" => json_encode(array(
            "segment_id" => intval($settings['segment_id']),
            "notification_id" => intval($settings['notification_id']),
            "adspace_id" => intval($settings['adspace_id']),
            "macros" => $macros
          )),
          "headers" => array(
            "Content-type" => "application/json; charset=utf-8",
            "token" => $settings['token'],
          ),
          'data_format' => 'body',
        );
        $response = wp_remote_post($api_url,$data_array);
        if (is_wp_error($response) || !is_array($response) || !isset($response['body'])) {
          $status = $response->get_error_code(); 				// custom code for WP_ERROR
          $error_message = $response->get_error_message();
          error_log('There was a '.$status.' error returned from InstantPush API: '.$error_message);

          return;
        } else {

          if (isset($response['body'])) {
            $response_body = json_decode($response['body'], true);
          }

          if (isset($response['response'])) {
              $status = $response['response']['code'];
          }

          if ($status != 200 || isset($response_body['error'])) {
            if (isset($response_body['error'])) {
              $status = $response_body['error'];
            }
            error_log('There was a '.$status.' error returned from InstantPush API');
          }

        }

      }

    }
}

add_action('add_meta_boxes', ['InstantPush_Send_Notification', 'add']);
add_action('save_post', ['InstantPush_Send_Notification', 'save']);
