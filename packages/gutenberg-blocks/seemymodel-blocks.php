<?php

/**
 * Registers the blocks using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function seemm_create_blocks_init() {
	register_block_type_from_metadata( __DIR__ . '/seemymodel-viewer');
  register_block_type_from_metadata( __DIR__ . '/seemymodel-group-viewer');
}
add_action( 'init', 'seemm_create_blocks_init' );

//Add seemymodel web components script to editor scripts
add_action( 'enqueue_block_editor_assets', function() {
  wp_register_script( 'smm', 'https://scripts.seemymodel.com/web-components/latest/web-components.js', null, null, false );
  wp_enqueue_script('smm');
} );


//Add seemymodel web components script to pages that use seemymodel block
add_action('wp_enqueue_scripts', function (){
  if(is_singular()){
     //We only want the script if it's a singular page
     $id = get_the_ID();
     if(has_block('see-my-model/viewer',$id) || has_block('see-my-model/group-viewer', $id)){
      wp_register_script( 'smm', 'https://scripts.seemymodel.com/web-components/latest/web-components.js', null, null, false );
      wp_enqueue_script('smm');
     }
  }
});