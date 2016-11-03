/**
 * Helper function copied from the internet
 * @param key
 * @param value
 * @returns {string}
 */
function insertParam(key, value) {
  key = encodeURI(key); value = encodeURI(value);

  var kvp = document.location.search.substr(1).split('&');

  var i=kvp.length; var x; while(i--)
  {
    x = kvp[i].split('=');

    if (x[0]==key)
    {
      x[1] = value;
      kvp[i] = x.join('=');
      break;
    }
  }
  if(i<0) {kvp[kvp.length] = [key,value].join('=');}

  return kvp.join('&');
}

/**
 * Helper function copied from the internet
 * @returns {{}}
 */
function searchToObject() {
  var pairs = window.location.search.substring(1).split("&"),
    obj = {},
    pair,
    i;

  for ( i in pairs ) {
    if ( pairs[i] === "" ) continue;

    pair = pairs[i].split("=");
    obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
  }

  return obj;
}

/**
 * onChange Event for the Admin Import Screen Relationship Drop Downs
 * @param event
 */
function acf_ti_update_post_type( event ){

  event.preventDefault();

  var $input = jQuery('[name="acf_ti_post_type"]')
    ,$old_input = jQuery('#acf_ti_post_id')
    ,url = 'http://' + window.location.hostname + window.location.pathname + '?' + insertParam( 'acf_ti_post_type', $input.val() )
    ,$loader = jQuery('.post-type-loader')
    ,$page_id = jQuery('#acf_ti_post_id');

  if($input){

    if($page_id.val() !== ''){
      url = url + '&acf_ti_post_id=' + $page_id.val();
    }

    $old_input.hide();
    $loader.show();

    jQuery.ajax({
      type: 'GET',
      url: url,
      success: function( data ){

        var $dom = jQuery( data )
          ,$new_section = jQuery('.acf-ti-relationship-builder',$dom) ;

        jQuery('.acf-ti-relationship-builder').replaceWith( $new_section );
      }
    });
  }
};


jQuery(function(){

  var params = searchToObject();

  if( typeof params.acf_ti_post_type !== 'undefined' ){

    jQuery('[name="acf_ti_post_type"]').val( params.acf_ti_post_type );
  }

  if( typeof params.acf_ti_post_id !== 'undefined' ){

    jQuery('[name="acf_ti_post_id"]').val( params.acf_ti_post_id );
  }

});