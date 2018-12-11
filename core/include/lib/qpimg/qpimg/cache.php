<?php

/**
 * Class qpimg_cache  
 * @package qpimg
 */

/**
 * Manage cache files (css, sprites) of qpimg projects.
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class qpimg_cache
{
    /**
     * Generate basic hash by incoming map-config array.
     * Public function
     *
     * @param array $map_cfg map-config array
     *
     * @return string md5 hash value
     */
    static public function get_basic_hash( $map_cfg )
    {
        return self::get_basic_hash_inner( $map_cfg, true );
    }

    /**
     * Private function generate basic hash by incoming map-config array.
     * Basic means: serialize map-config array and count md5 hash sum.
     *
     * @param array $map_cfg map-config array
     * @param bool $is_root_cfg flag that show is incoming $map_cfg is main map.
     *      If true  -> then will check attach maps.
     *      If false -> then attach maps will ignore.
     *
     * @return string md5 hash value
     */
    static private function get_basic_hash_inner( $map_cfg, $is_root_cfg )
    {
        unset( $map_cfg['hash'] );
        
        if( $is_root_cfg && isset( $map_cfg['attach'] ) && is_array( $map_cfg['attach'] ) )
        {
            $map_cfg['attach:hash'] = array();

            foreach( $map_cfg['attach'] as $attach_map_id )
            {
                $attach_map_cfg = qpimg_config::get_map( $attach_map_id );

                $attach_map_hash = $attach_map_cfg
                                 ? self::get_basic_hash_inner( $attach_map_cfg, false )
                                 : false;

                $map_cfg['attach:hash'][ $attach_map_id ] = $attach_map_hash;
            }
        }

        ksort( $map_cfg );

        return md5( serialize( $map_cfg ) );
    }

    /**
     * Generate hash of media-items of incoming map-config.
     *  
     * This function detect filesize, last file time modify for each 
     * media item of the map. Then concat this values and count md5 hash sum
     * 
     * Almost function save counted values in buffer. If scripts try
     * recount media-hash sum again - it's returned from buffer.
     * 
     * @param array $map_cfg map-config array
     * 
     * @return string md5 hash value
     */
    static public function get_media_hash( $map_cfg )
    {
        static $buffer = array();
        
        if( isset( $buffer[ $map_cfg['id'] ] ) === false )
        {
            $media_string = '';

            foreach( $map_cfg['objects'] as $media_id => $media_cfg ) 
            {
                list( $media_id, $presets, $media_source, $media_cfg ) = qpimg_media::parse_media_values( $media_id, $media_cfg );
                
                $media_string .= "$media_id:$media_source:";

                if( file_exists( $media_source ) === false )
                {
                    $media_string .= 'not_exists;';
                    continue;
                }
                
                $media_string .= filesize( $media_source ) . ':' . filemtime( $media_source ) . ';';
            }

            $buffer[ $map_cfg['id'] ] = md5( $media_string );
        }
        
        return $buffer[ $map_cfg['id'] ];
    }
    
    /**
     * Generate path for cache-file by map-config array, extenstion of file and 
     * key-value. 
     * 
     * The struct is:
     * cache-folder/map_id[.map_hash_value][.key].extenstion
     * 'map_hash_value' and 'key' - optional
     * 
     * @param array $map_cfg map-config array
     * 
     * @param string $ext extension of file
     * 
     * @param string $key key value
     * 
     * @param bool $is_url bool value, is need generate PATH(=false) or URL(=true) to file 
     * 
     * @return string cache-file path 
     */
    static public function gen_filename( $map_cfg, $ext, $key = null, $is_url = false )
    {
        if( $ext == 'crc' )
        {
            $basedir = qpimg_config::get_option('private_cache_basedir');
        }
        else
        {
            $basedir = qpimg_config::get_option( $is_url ? 'public_cache_dir_src' : 'public_cache_basedir' );
        }
        
        if( $basedir === false )
        {
            qpimg_logger::write( "QPIMG_ERROR: Cannot detect cache_basedir", __FILE__, __LINE__ );
            die();
        }
        
        return $basedir . $map_cfg['id']
             . ( $map_cfg['hash'] ? ".{$map_cfg['hash']}" : "" )
             . ( $key !== null ? ".{$key}" : "" )
             . ".$ext";
    }
    
    /**
     * Generate valid css-source of cached css-file. 
     * Each map may has 1 of 2 css files.
     * if 1 css-file  - this is <standard> css only    
     * if 2 css-files - this is <standard> & <IE7--> css files
     * 
     * This function detect if current user-browser is IE7 of lower then
     * check if css-file <IE7--> exists. If not exists then return
     * source to <standard> css file.
     * 
     * @param array $map_cfg map-config array
     * 
     * @param boold $is_grouper grouper flag 
     * 
     * @return string css-source 
     */
    static public function get_valid_css_source( $map_cfg, $is_grouper )
    {
        $filename_keys = array();

        if( $is_grouper === true )
        {
            if( qpimg_utils::check_is_IE7mm() === true )
            {
                $filename_keys[] = qpimg::CSSGRP_FILE_KEY_DATAURI_REJECT;
            }        
            $filename_keys[] = qpimg::CSSGRP_FILE_KEY_DEFAULT;
        }
        else
        {
            if( qpimg_utils::check_is_IE7mm() === true )
            {
                $filename_keys[] = qpimg::CSS_FILE_KEY_DATAURI_REJECT;
            }
            $filename_keys[] = qpimg::CSS_FILE_KEY_DEFAULT;
        }

        foreach( $filename_keys as $filename_key )
        {
            $filename = self::gen_filename( $map_cfg, 'css', $filename_key );
            
            if( file_exists( $filename ) === true )
            {
                return $filename;
            }
        }
        
        return false;
    }

    /**
     * Validate cache. 
     * Check if cache exists and valid of current map. 
     * 
     * @param array $map_cfg map-config array
     * 
     * @return bool is-validate 
     */
    static public function validate( $map_cfg )
    {
        if( self::get_valid_css_source( $map_cfg, isset( $map_cfg['attach'] ) ) === false )
        {
            self::validation_failed( $map_cfg );
            return false;
        }

        if( $map_cfg['verbose_check'] === true )
        {
            $crc_filename = self::gen_filename( $map_cfg, 'crc' );

            if( file_exists( $crc_filename ) === false )
            {
                self::validation_failed( $map_cfg );
                return false;
            }
            
            $is_value = @file_get_contents( $crc_filename ) == ( $map_cfg['hash'] . ':' . self::get_media_hash( $map_cfg ) );

            if( $is_value === false )
            {
                self::validation_failed( $map_cfg );
                return false;
            }
        }

        return true;
    }

    /**
     * Clean invalid cache files
     *
     * @param array $map_cfg map-config array
     *
     * @return true
     */
    static private function validation_failed( $map_cfg )
    {
        $folders = array();
        $folders[ qpimg_config::get_option('private_cache_basedir') ] = true;
        $folders[ qpimg_config::get_option('public_cache_basedir') ] = true;

        $map_idd = $map_cfg['id'].'.';
        $map_len = strlen( $map_idd );

        foreach( $folders as $folder => $const )
        {
            if( $folder === false || is_dir( $folder ) === false )
            {
                continue;
            }

            $do = dir( $folder );

            while( false !== ( $fname = $do->read() ) )
            {
                if( $fname == '.' || $fname == '..' || is_dir( $folder.$fname ) )
                {
                    continue;
                }

                if( substr( $fname, 0, $map_len ) != $map_idd )
                {
                    continue;
                }

                @ unlink( $folder.$fname );
            }
            $do->close();
        }

        return true;
    }

    /**
     * Clean cache files of current map.
     * 
     * @param array $map_cfg map-config array
     * 
     * @param bool $is_only_css_groupers if true then clean only group-css files.
     *      otherwise clean all cache files of current map.
     * 
     * @return bool true 
     */
    static public function clean( $map_cfg, $is_only_css_groupers = false )
    {
        if( $is_only_css_groupers === true )
        {
            $clean_groups = array(
                'css:' . qpimg::CSSGRP_FILE_KEY_DEFAULT,
                'css:' . qpimg::CSSGRP_FILE_KEY_DATAURI_REJECT,
            );
        }
        else
        {
            $clean_groups = array(
                'crc',
                'css:' . qpimg::CSS_FILE_KEY_DEFAULT,
                'css:' . qpimg::CSS_FILE_KEY_DATAURI_REJECT,
                'css:' . qpimg::CSSGRP_FILE_KEY_DEFAULT,
                'css:' . qpimg::CSSGRP_FILE_KEY_DATAURI_REJECT,
                $map_cfg['save_format'] . ':' . qpimg::SPRITE_FILE_KEY_DEFAULT,
                $map_cfg['save_format'] . ':' . qpimg::SPRITE_FILE_KEY_DATAURI_REJECT,
            );
        }

        foreach( $clean_groups as $clean_item )
        {
            list( $ext, $key ) = explode( ':', $clean_item, 2 );
            
            $filename = self::gen_filename( $map_cfg, $ext, $key );

            if( file_exists( $filename ) === true )
            {
                @unlink( $filename );
            }
        }

        return true;
    }

    /**
     * Redirect (mean read from file and output) to CSS-file of current map.
     * Almost generate header-values.
     * 
     * @param array $map_cfg map-config array
     * 
     * @return bool is redirect done 
     */
    static public function redirect( $map_cfg )
    {
        $filename = self::get_valid_css_source( $map_cfg, isset( $map_cfg['attach'] ) );

        if( $filename === false )
        {
            return false;
        }
        
        //---------------------------------------------------------------------

        $cache_timeout              = qpimg_config::get_option('cache_time_expire');

        $headers = array();
        $headers['Expires']         = gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_timeout );
        $headers['Last-Modified']   = gmdate('D, d M Y H:i:s \G\M\T', filemtime( $filename ) );
        $headers['ETag']            = "\"qpimg-{$map_cfg['id']}-{$map_cfg['hash']}\""; 
        
        //---------------------------------------------------------------------

        $if_none_match = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;

        $if_modified_since = isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ? stripslashes( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) : false;
        
        if( $if_none_match && $if_none_match == $headers['ETag'] )
        {
            $headers[':Response-Code'] = 304;

            $filename = false;
        }
        elseif( $if_modified_since && $if_modified_since == $headers['Last-Modified'] )
        {
            $headers[':Response-Code'] = 304;

            $filename = false;
        }
        else
        {
            $headers['Content-Type']    = 'text/css';
            $headers['Cache-Control']   = "max-age={$cache_timeout}, public, must-revalidate";
        }

        qpimg_utils::send_headers( $headers );
        
        if( $filename !== false )
        {
            @readfile( $filename );
        }

        return true;
    }
}
