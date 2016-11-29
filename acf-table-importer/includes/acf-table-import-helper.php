<?php

/**
 * @return array
 */
function acf_ti_set_defaults()
{
  return [
    'p'=> [
      'o' => [
        'uh' => 1
      ]
    ],
    'c' => [],
    'h' => [],
    'b' => [],
  ];
}

/**
 * @param string $filename
 * @param string $delimiter
 * @return array|bool
 */
function acf_ti_csv_to_array($filename='', $delimiter=',')
{
  if(!file_exists($filename) || !is_readable($filename))
    return FALSE;

  $header = NULL;
  $data = array();
  if (($handle = fopen($filename, 'r')) !== FALSE)
  {
    while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
    {
      if(!$header)
        $header = $row;
      else{
        $data[] = array_combine($header, $row);
      }

    }

    fclose($handle);

    if(!empty($data) && is_array($data)){
      foreach($data as $key=>$row){
        foreach($row as $header=>$col){
          $row[ addslashes( strval( $header ) ) ] = addslashes( $col );
        }
        $data[ $key ] = $row;
      }
    }
  }
  return $data;
}

/**
 * @param $table
 * @param $data
 * @return array
 */
function acf_ti_set_header( $table, $data )
{
  $headers = array_keys( $data[0] );

  foreach ( $headers as $header ) {
    $table['c'][] = ['p' => ''];
    $table['h'][] = ['c' => (string)$header];
  }

  return $table;
}

/**
 * @param $table
 * @param $data
 */
function acf_ti_set_cols_and_rows( $table, $data)
{
  foreach ( $data as $k => $row ) {
    $table['b'][ strval( $k ) ] = [];
    foreach( $row as $item )
      $table['b'][ strval( $k ) ][] = [ 'c' => addslashes($item) ];
  }

  return $table;
}

/**
 * @param int $length
 * @return string
 */
function acf_ti_generate_random_string( $prefix, $length = 10 ) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $prefix.$randomString;
}

function act_ti_set_meta_set( $post_id, $field_name_prefix, $table ) {

  $post_meta = get_post_meta( $post_id );
  $post_table_data = preg_grep("/^".$field_name_prefix."_[0-9]_data/", array_keys( $post_meta ));
  $results = [
    'new' => [],
    'update' => []
  ];

  if( !empty( $post_table_data ) ) {

    $results['new'][ $field_name_prefix.'_'.count( $post_table_data ).'_data' ] = $table;
    $results['new'][ '_'.$field_name_prefix.'_'.count( $post_table_data ).'_data' ] = acf_ti_generate_random_string( 'field_' );

    foreach( $post_meta as $meta_key => $meta_value ){

      if( $meta_key === $field_name_prefix
          && !empty( $meta_value[0] ) ) {

        $serialized_meta_data = unserialize( $meta_value[0] );
        array_push( $serialized_meta_data, trim($field_name_prefix, "s").'_data' );
        $results['update'][ $field_name_prefix ] = $serialized_meta_data;

      }
    }
  }else{

    $results['new'][ $field_name_prefix.'_0_data' ] = $table;
    $results['new'][ '_'.$field_name_prefix.'_0_data' ] = acf_ti_generate_random_string( 'field_' );
    $results['update'][ $field_name_prefix ] = [ trim($field_name_prefix, "s").'_data' ];

  }

  return $results;
}