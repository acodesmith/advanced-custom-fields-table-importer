<?php
/*
Plugin Name: Advanced Custom Fields: Table Import
Plugin URI: https://acodesmith.com
Description: This free Add-on adds the ability to import CSV files into a table field type for the Advanced Custom Fields plugin
Version: 0.0.1
Author: Adam Smith
Author URI: https://acodesmith.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Register a custom menu page.
 */
function acf_ti_register_menu_page() {
  add_submenu_page(
    'edit.php?post_type=acf-field-group',
    __( 'Import to Table', 'textdomain' ),
    __( 'Import to Table', 'textdomain' ),
    'manage_options',
    'advanced-custom-fields-table-import/acf-table-import-admin.php'
  );
}
add_action( 'admin_menu', 'acf_ti_register_menu_page' );

/**
 * Admin Action for the import form
 */
function acf_ti_admin_action()
{

  include_once 'includes/acf-table-import-helper.php';

  $redirect = $_SERVER['HTTP_REFERER'];
  $data = acf_ti_csv_to_array( $_FILES['acf_ti_file']['tmp_name'], $_POST['acf_ti_delimiter'] );
//  print_r($data);die;
  if( !empty( $data ) && is_array( $data ) && !empty( $_POST['acf_ti_field_name'] ) ) {

    //clean empty data option
    if( !empty( $_POST['acf_ti_array_filter'] ) && $_POST['acf_ti_array_filter'] === 'yes' )
      $data = array_filter(array_map('array_filter', $data));

    //build table data
    $table = acf_ti_set_defaults();
    $table = acf_ti_set_header( $table, $data );
    $table = acf_ti_set_cols_and_rows( $table, $data );

    //build wp_postmeta options
    $meta_data = act_ti_set_meta_set( $_POST['acf_ti_post_id'], $_POST['acf_ti_field_name'], json_encode( $table ) );

    //add new post meta
    if( !empty($meta_data['new']) ){
      foreach( $meta_data['new'] as $key =>$data ){
        add_post_meta( $_POST['acf_ti_post_id'], $key, $data );
      }
    }

    //update old post meta
    if( !empty($meta_data['update']) ){
      foreach( $meta_data['update'] as $key =>$data ){
        update_post_meta( $_POST['acf_ti_post_id'], $key, $data );
      }
    }

    $redirect .= '&notification='.urlencode("The CSV has been successfully imported!");
  }

  if( empty( $_POST['acf_ti_field_name'] ) ){
    $redirect .= '&error='.urlencode("Missing the Advanced Custom Field Group!");
  }

  wp_redirect( $redirect );

  exit();
}
add_action( 'admin_action_acf_ti', 'acf_ti_admin_action' );

/**
 * Load JS on import page only
 * @param $hook
 */
function my_enqueue( $hook ) {

  if ( 'advanced-custom-fields-table-import/acf-table-import-admin.php' != $hook ) {
    return;
  }

  wp_enqueue_script( 'acf_ti_admin_script', plugin_dir_url( __FILE__ ) . 'js/acf_ti_admin.js' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

/**
 * @param $data
 * @return mixed
 */
function acf_ti_character_converter($data)
{
  $patterns = [];
  $patterns[0] = '/u201d/';
  $patterns[1] = '/u00b0/';
  $patterns[2] = '/u2019/';
  $replacements = [];
  $replacements[0] = '"';
  $replacements[1] = '&#186;';
  $replacements[2] = '\'';
  return  preg_replace($patterns, $replacements, $data);
}