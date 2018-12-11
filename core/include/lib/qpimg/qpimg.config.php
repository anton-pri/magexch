<?php

/**
 * Config data
 *
 * @package qpimg
 */

global $current_location, $app_dir, $qpimg_config;

$qpimg_app_dir = str_replace( '\\', '/', $app_dir);

/**
 * Define core of qpimg core script
 */
$qpimg_config['core_basedir'] = str_replace( '\\', '/', dirname( __FILE__ ) . '/qpimg/' );



/**
 * URL (absolute or reletive) for main qpimg script.
 */
$qpimg_config['core_script_src'] = $current_location . '/core/include/lib/qpimg/qpimg.php';



/**
 * URL (absolute or reletive) for empty (transparent) image.
 */
$qpimg_config['empty_image_src'] = $current_location . '/images/empty.gif';



/**
 * Destination for private cache folder (may be not accessable for web-users).
 */
$qpimg_config['private_cache_basedir'] = 'var/cache/qpimg.cache/';



/**
 * Destination for public cache folder.
 */
$qpimg_config['public_cache_basedir'] = 'var/cache/qpimg.cache/';



/**
 * URL for public cache folder (must be accessable for web-users).
 */
$qpimg_config['public_cache_dir_src'] = $current_location . '/' . $qpimg_config['public_cache_basedir'];



/**
 * Destination for log file. If values is <FALSE> - error will show as HTML-code on display
 */
$qpimg_config['log_file'] = $qpimg_app_dir . '/var/log/qpimg.log/qpimg.log';



/**
 * Limit (in bytes) of files for data:URI-images
 * Default value is 3072 bytes
 */
$qpimg_config['dataURI_filesize_limit'] = 3072;



/**
 * Time offset of expire period (in seconds)
 * Default value is 5 years
 */
$qpimg_config['cache_time_expire'] = 5 * 365 * 24*60*60;



/**
 * Array of user presets. 
 * 
 * Struct: each item of array is:
 * <key> - preset_id
 * <value> - array of pairs ( key => value )
 */
$qpimg_config['presets'] = array(
    '@' => array( // default preset -> auto assign for all objects
    ),

    'whb' => array( // 'whb' is set 'White & Height & Border' values
        'css-set:width' => true,
        'css-set:height' => true,
        'css-set:border' => '0',
    	'css-set:bgcolor' => '#FFFFFF',
    ),
);


/**
 * Array of user maps.
 */
//$global_map_stamp = 0;
    // Changing this value then hash value of all maps will change (need for debuging).
    // For regenerate all maps everytime page view use $global_map_stamp = time();
    // See in cw_generate_css_sprites

$qpimg_config['maps'] = $maps_objects;/*array(
    'main' => array(
        'orientation' => 'static',
        'save_format' => 'gif',
        'map_width' => 500,
        'bgcolor' => '#00ff00',
        'transparent_color' => '#00ff00',
        'default_presets' => '@whb', // may assign list or presets as '@preset1@preset2@preset3...'
        'objects' => array(			
            'logo' => array(
                'source' => 'sample/logo.gif',
                'css-selector' => 'img.mylogo'
            ),
            'form1' => 'sample/form1.gif',
            'form3' => 'sample/form3.gif',
            'form7' => 'sample/form7.gif',
            'form9' => 'sample/form9.gif',
            'dot' => array(
                'source' => 'sample/dot.gif',
                'css-selector' => array(
                    'ul.mylist li',
                    'ul.another_mylist li.item'
                ),
                'space' => '0 500px 30px 0',
                'presets' => '@~whb', // '~' means 'unset preset'. May by as '@preset1@~preset2...'
            ),
            'button' => 'sample/button.gif',
            'button_over' => 'sample/button2.gif',
            'i1' => 'sample/item1.gif',
            'i2' => 'sample/item2.gif',
            'i3' => 'sample/item3.gif',
            'i4' => 'sample/item4.gif',
            'i5' => 'sample/item5.gif',
            'i6' => 'sample/item6.gif',
        ),
        'attach' => array( 'horiz', 'vert' ),
        'mapstamp' => $global_map_stamp,
    ),
    
    'horiz' => array(
        'orientation' => 'repeat_x',
        'save_format' => 'png',
        'map_width' => 8,
        'objects' => array(
            'form2' => 'sample/form2.gif',
            'form8' => 'sample/form8.gif',
        ),
        'mapstamp' => $global_map_stamp,
    ),

    'vert' => array(
        'orientation' => 'repeat_y',
        'save_format' => 'png',
        'map_height' => 8,
        'objects' => array(
            'form4' => 'sample/form4.gif',
            'form6' => 'sample/form6.gif',
        ),
        'mapstamp' => $global_map_stamp,
    ),
);*/



function qpimg_user_get_map( $map_id )
{
    return false;
}


function qpimg_user_get_preset( $preset_id )
{
    return false;
}
