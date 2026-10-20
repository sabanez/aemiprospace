<?php 
/**
* Esse código foi baseado em https://wordpress.stackexchange.com/questions/339436/removing-panels-meta-boxes-in-the-block-editor
* @author Christine Cooper
*/
function aemi_cc_gutenberg_register_files() {
    // script file
    wp_register_script(
        'aemi-block-script',
        get_stylesheet_directory_uri() .'/assets/js/block-script.js', // adjust the path to the JS file
        array( 'wp-blocks', 'wp-edit-post' )
    );
    // register block editor script
    register_block_type( 'aemi/remove-default-gutemberg-block-files', array(
        'editor_script' => 'aemi-block-script'
    ) );

}
add_action( 'init', 'aemi_cc_gutenberg_register_files' );