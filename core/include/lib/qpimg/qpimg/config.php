<?php

/**
 * Class qpimg_config
 * @package qpimg
 */

/**
 * Safe get user config values.
 * If values undefined then return default values. 
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class qpimg_config
{
    /**
     * Return const value from user qpimg_config class by key
     * 
     * @param string $key
     * 
     * @return mix value asked value or false 
     */
    static public function get_option( $key )
    {
        $qpimg_config = $GLOBALS['qpimg_config'];

        switch( $key )
        {
            case 'private_cache_basedir':
            case 'public_cache_basedir':
            case 'public_cache_dir_src':
            {
                return isset( $qpimg_config[ $key ] ) 
                     ? rtrim( $qpimg_config[ $key ], '/' ) . '/'
                     : false;
            }

            case 'core_script_src':
            case 'empty_image_src':
            case 'log_file':
            {
                return isset( $qpimg_config[ $key ] ) 
                     ? $qpimg_config[ $key ]
                     : false;
            }

            case 'cache_time_expire':
            {
                return isset( $qpimg_config[ $key ] ) 
                     ? (int) $qpimg_config[ $key ]
                     : (int) 5 * 365.25 * 24*60*60;
            }

            case 'dataURI_filesize_limit':
            {
                return isset( $qpimg_config[ $key ] ) 
                     ? (int) $qpimg_config[ $key ]
                     : 3*1024;
            }
        }

        return false;
    }

    /**
     * Check map_id value for allow chars and return map-config array by map_id.
     * 
     * First check if user qpimg_config class has get_map function and 
     * ask data by this function. Otherwise get data from map-array. 
     * Otherwise return false.
     * 
     * @param string $map_id
     * 
     * @return array map-config data or false 
     */
    static public function get_map( $map_id )
    {
        $qpimg_config = $GLOBALS['qpimg_config'];

        if( preg_match( '/[^a-zA-Z0-9_]/', $map_id ) > 0 )
        {
            qpimg_logger::write( "QPIMG_WARNING: Used illegal chars in map name '$map_id'", __FILE__, __LINE__ );
            return false;
        }
        
        $map_cfg = false;
        
        if( function_exists( 'qpimg_user_get_map' ) === true )
        {
            $map_cfg = qpimg_user_get_map( $map_id );
        }
        
        if( ! $map_cfg )
        {
            if( isset( $qpimg_config['maps'] ) === false || isset( $qpimg_config['maps'][ $map_id ] ) === false )
            {
                qpimg_logger::write( "QPIMG_WARNING: Asked map '$map_id' is not exists", __FILE__, __LINE__ );
                return false;
            }

            $map_cfg = $qpimg_config['maps'][ $map_id ];
        }

        $map_cfg['id'] = $map_id;

        if( isset( $map_cfg['hash'] ) === false )
        {
            $map_cfg['hash'] = qpimg_cache::get_basic_hash( $map_cfg );
        }

        return $map_cfg;
    }
    
    /**
     * Return array of map_id of all exists map-configs arrays
     * 
     * @return array 
     */
    static public function get_map_keys()
    {
        $qpimg_config = $GLOBALS['qpimg_config'];

        if( isset( $qpimg_config['maps'] ) === false || is_array( $qpimg_config['maps'] ) === false )
        {
            return array();
        }
        
        return array_keys( $qpimg_config['maps'] );
    }

    /**
     * Return preset values array by preset_id.
     * 
     * First check if user qpimg_config class has get_preset function and 
     * ask data by this function. Otherwise get data from presets-array. 
     * Otherwise return false.
     * 
     * @param string $preset_id
     * 
     * @return array preset values
     */
    static public function get_preset( $preset_id )
    {
        $qpimg_config = $GLOBALS['qpimg_config'];

        if( function_exists( 'qpimg_user_get_preset' ) === true )
        {
            $preset = qpimg_user_get_preset( $preset_id );

            if( $preset )
            {
                return $preset;
            }
        }

        if( isset( $qpimg_config['presets'] ) === false || 
            isset( $qpimg_config['presets'][ $preset_id ] ) === false || 
            is_array( $qpimg_config['presets'][ $preset_id ] ) === false )
        {
            return $preset_id == '@' ? array() : false;
        }

        return $qpimg_config['presets'][ $preset_id ];
    }
}
