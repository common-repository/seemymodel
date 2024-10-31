<?php

//Init rest api
add_action( 'rest_api_init', function() {
	//Add endpoint to access models
    register_rest_route( 'see-my-model', '/models', array(
        'methods'             => 'GET',
        'permission_callback' => 'seemm_rest_api_permission_check', // Restrict access only to specific users
        'callback'            => function ( $request ) {
			$seemymodel_options_options = get_option( 'seemymodel_options_option_name' ); // Array of All Options
			$admin_seemymodel_jwt_token = $seemymodel_options_options['seemymodel_user_password_1']; // SeeMyModel user password
			
			$endpoint = 'https://api.seemymodel.com/ar-models';
			$options = [
				'headers'     => [
					'Authorization' => 'Bearer ' . $admin_seemymodel_jwt_token,
				],
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'sslverify'   => true,
			];
			
			$response = wp_remote_get( $endpoint, $options );
			// Response body.
			$body = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				return new WP_Error( 'see-my-model', $error_message, array( 'status' => 500 ) );
			} else {
				$responseData = json_decode( $body, true );
				$status = wp_remote_retrieve_response_code( $response );
				if ($status == 200)
					return array_map(function($item) {
						return array(
							'modelID' => $item['_id'], 
							'name' => $item['name'], 
							'posterURL' =>  $item['image'], 
						);
					}, $responseData);
				else if ($status == 401) {
					return new WP_Error( 'see-my-model', 'Unauthorized user', array( 'status' => 401 ) );
				} else {
					return new WP_Error( 'see-my-model', 'Unknown error', array( 'status' => 500 ) );
				}
				
			}
        },
    ) );
	//Add endpoint to access folders
	register_rest_route( 'see-my-model', '/folders', array(
        'methods'             => 'GET',
        'permission_callback' => 'seemm_rest_api_permission_check', // Restrict access only to specific users
        'callback'            => function ( $request ) {
			$seemymodel_options_options = get_option( 'seemymodel_options_option_name' ); // Array of All Options
			$admin_seemymodel_jwt_token = $seemymodel_options_options['seemymodel_user_password_1']; // SeeMyModel user password
			
			$endpoint = 'https://api.seemymodel.com/folders/getfolders';
			$options = [
				'headers'     => [
					'Authorization' => 'Bearer ' . $admin_seemymodel_jwt_token,
				],
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'sslverify'   => true,
			];
			
			$response = wp_remote_get( $endpoint, $options );
			// Response body.
			$body = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				return new WP_Error( 'see-my-model', $error_message, array( 'status' => 500 ) );
			} else {
				$responseData = json_decode( $body, true );
				$status = wp_remote_retrieve_response_code( $response );
				if ($status == 200){
					$folders = array();
					$default = '';
					foreach ($responseData as $item) {
						if ($item['access'] =='public') {
							array_push($folders, array(
								'name' => $item['name'],
								'id' => $item['publicAccessKey']
							));
						}
					}
					return $folders;
				}
				else if ($status == 401) {
					return new WP_Error( 'see-my-model', 'Unauthorized user', array( 'status' => 401 ) );
				} else {
					return new WP_Error( 'see-my-model', 'Unknown error', array( 'status' => 500 ) );
				}
				
			}
        },
    ) );
}, 10, 1 );


function seemm_rest_api_permission_check() {
    // Restrict endpoint to only users who have the edit_posts capability.
    if ( ! current_user_can( 'edit_posts' ) ) {
        return false;
    }
    return true;
}