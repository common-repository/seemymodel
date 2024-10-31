<?php
// Test to see if WooCommerce is active. Return from module if not installed
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
if ( !in_array( $plugin_path, wp_get_active_and_valid_plugins() ) ) {
    return;
}

// Display Model Field on admin product page
add_action('woocommerce_product_options_general_product_data', function(){
    global $post;
    
    //get list of user models
    $seemymodel_options_options = get_option( 'seemymodel_options_option_name' ); // Array of All Options
    $admin_seemymodel_jwt_token = isset($seemymodel_options_options['seemymodel_user_password_1']) ? $seemymodel_options_options['seemymodel_user_password_1'] : ''; // SeeMyModel user password
    
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
        woocommerce_wp_select(
            array(
                'id' => '_product_seemymodel_armodel_id',
                'label' => __('Product 3D Model', 'see-my-model'),
                'desc_tip' => false,
                'description' => sprintf(__('Unexpected error: %s', 'see-my-model'), $error_message),
                'options' => array( 'none' => __('None','see-my-model')),
            )
        );
    } else {
        $responseData = json_decode( $body, true );
        $status = wp_remote_retrieve_response_code( $response );
        if ($status == 200){
            $options = array(
                'none' => __('None','see-my-model')
            );
            foreach ($responseData as $item) {
                $options[$item['_id']] = $item['name'];
            }
            $product = wc_get_product($post->ID);
            $selected_value = $product->get_meta('_product_seemymodel_armodel_id');
            if (is_null($selected_value)) $selected_value = 'none';
            woocommerce_wp_select(
                array(
                    'id' => '_product_seemymodel_armodel_id',
                    'label' => __('Product 3D Model', 'see-my-model'),
                    'value' => $selected_value,
                    'options' => $options,
                )
            );
        }
        else if ($status == 401) {
            woocommerce_wp_select(
                array(
                    'id' => '_product_seemymodel_armodel_id',
                    'label' => __('Product 3D Model', 'see-my-model'),
                    'desc_tip' => false,
                    'description' => __('Authorization error. <a href="options-general.php?page=seemymodel-options" target="_blank"> Click here </a> to log into your seemymodel.com account.', 'see-my-model'),
                    'options' => array( 'none' => __('None','see-my-model')),
                )
            );
            
            
        } else {
            woocommerce_wp_select(
                array(
                    'id' => '_product_seemymodel_armodel_id',
                    'label' => __('Product 3D Model', 'see-my-model'),
                    'desc_tip' => false,
                    'description' =>  sprintf(__('Unexpected error: %1$s %2$s', 'see-my-model'), $status, $body),
                    'options' => array( 'none' => __('None','see-my-model')),
                )
            );
        }
    }
});

// Save Model Field on admin product page
add_action('woocommerce_process_product_meta', function($post_id)
{
    $arModelID = $_POST['_product_seemymodel_armodel_id'];
    if (!empty($arModelID)){
        if ($arModelID == 'none') {
            delete_post_meta($post_id, '_product_seemymodel_armodel_id');
            delete_post_meta($post_id, '_product_seemymodel_3d_viewer_embed_tag');
        } else {
            update_post_meta($post_id, '_product_seemymodel_armodel_id', esc_attr($arModelID));
            update_post_meta($post_id, '_product_seemymodel_3d_viewer_embed_tag', '<see-my-model class="smm-viewer" model-access-key="' . esc_attr($arModelID) . '"></see-my-model>');
        }
    }
});



/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	// Adds the new tab
	global $post;
    $product = wc_get_product($post->ID);
    $product_seemymodel_3d_viewer_embed_tag = $product->get_meta('_product_seemymodel_3d_viewer_embed_tag');
    if ($product_seemymodel_3d_viewer_embed_tag){
        $tabs['seemymodel_3dview'] = array(
            'title' 	=> __( '3D View', 'see-my-model' ),
            'priority' 	=> 50,
            'callback' 	=> function() use($product){
                echo seemm_product_3d_view($product);
            }
        );
    }
	return $tabs;
} );


/**
 * Returns html tag to embed 3d viewer
 * 
 * $product - woocommerce product object or its id
 */
function seemm_product_3d_view( $product ){
    //Retrieve product if passed argument is string representing product id
    if (is_integer($product)) $product = wc_get_product($product);
    //Guard
    if (!is_object($product)) return '<p>Invalid product object type: ' . gettype($product) . '</p>';
    // Retrieve and return 3d view embed tag
    $product_seemymodel_3d_viewer_embed_tag = $product->get_meta('_product_seemymodel_3d_viewer_embed_tag');
    if (is_null($product_seemymodel_3d_viewer_embed_tag)) return  '<p>Product does not have 3D View</p>';
    else return $product_seemymodel_3d_viewer_embed_tag;
}

//Add seemymodel web components script to product pages
add_action('wp_enqueue_scripts', function(){
    if(is_product()){
        wp_register_script( 'smm', 'https://scripts.seemymodel.com/web-components/latest/web-components.js', null, null, false );
        wp_enqueue_script('smm'); 
        wp_register_style('smm', plugins_url('see-my-model/style.css'));
        wp_enqueue_style('smm');
    }
});

