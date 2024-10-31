<?php
class Seemymodel_Viewer_Widget extends \Elementor\Widget_Base {
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_script( 'smm', 'https://scripts.seemymodel.com/web-components/latest/web-components.js', null, null, false );
    }

    public function get_name() {
        return 'seemymodel-viewer';
    }
 
    public function get_title() {
        return __( 'SeeMyModel Viewer', 'see-my-model' );
    }
 
    public function get_icon() {
        return 'fas fa-cube';
    }
 
    public function get_categories() {
        return [ 'general' ];
    }

    public function get_script_depends() {
        return [ 'smm' ];
    }
 
    protected function _register_controls() {
 
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'see-my-model' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
 
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
            $this->add_control(
                'err', [
                    'label' => __( 'Error', 'see-my-model' ),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => sprintf(__('Unexpected error: %s', 'see-my-model'), $error_message),
                ]
            );
        } else {
            $responseData = json_decode( $body, true );
            $status = wp_remote_retrieve_response_code( $response );
            if ($status == 200){
                $options = array();
                foreach ($responseData as $item) {
                    $options[$item['_id']] = $item['name'];
                }
                $this->add_control(
                    'model', [
                        'label' => __( 'Model', 'see-my-model' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => $options,
                        'default' => '508e20b1ba5a21f628717c37',
                    ]
                );
                
            }
            else if ($status == 401) {
                $this->add_control(
                    'err', [
                        'label' => __( 'Error', 'see-my-model' ),
                        'type' => \Elementor\Controls_Manager::RAW_HTML,
                        'raw' => __('Authorization error. <a href="options-general.php?page=seemymodel-options" target="_blank"> Click here </a> to log into your seemymodel.com account.', 'see-my-model'),
                    ]
                );
            } else {
                $this->add_control(
                    'err', [
                        'label' => __( 'Server error', 'see-my-model' ),
                        'type' => \Elementor\Controls_Manager::RAW_HTML,
                        'raw' => sprintf(__('Unexpected error: %1$s %2$s', 'see-my-model'), $status, $body)
                    ]
                );
            }
        }       

        $this->add_control(
			'width',
			[
				'label' => __( 'Viewer Width', 'see-my-model' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw' ],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
                    'vw' => [
						'min' => 0,
						'max' => 100,
					],
                    'em' => [
						'min' => 0,
						'max' => 500,
					],
                    'rem' => [
						'min' => 0,
						'max' => 500,
					],
				],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
				'selectors' => [
					'{{WRAPPER}} see-my-model' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'height',
			[
				'label' => __( 'Viewer Height', 'see-my-model' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw' ],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
                    'vw' => [
						'min' => 0,
						'max' => 100,
					],
                    'em' => [
						'min' => 0,
						'max' => 500,
					],
                    'rem' => [
						'min' => 0,
						'max' => 500,
					],
				],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
				'selectors' => [
					'{{WRAPPER}} see-my-model' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
			'locale',
			[
				'label' => __( 'Language', 'see-my-model' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
                    'auto' => __('Auto', 'see-my-model'),
                    'pl' => __('Polish', 'see-my-model'),
                    'en' => __('English', 'see-my-model'),
                ),
                'default' => 'auto',
			]
		);

        $this->end_controls_section();
 
    }
     
    protected function render() {
        // generate the final HTML on the frontend using PHP
        $settings = $this->get_settings_for_display();
    
        ?>
            <see-my-model 
                access-key=<?php echo esc_attr($settings['model']) ?> 
                <?php 
                    if ($settings['locale'] != 'auto') {
                        echo esc_attr('locale=' . $settings['locale']);
                    }
                ?>
            ></see-my-model>
        <?php
        
    }
}