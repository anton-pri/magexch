<?php

/**
 * Class qpimg
 * @package qpimg
 */

require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/config.php' );
require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/logger.php' );
require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/cache.php' );
require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/media.php' );
require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/media_item_cfg.php' );
require_once( $GLOBALS['qpimg_config']['core_basedir'] . '/utils.php' );

/**
 * qpimg - dynamic-generator of css-sprites images & css files.
 *
 * Require PHP 5, GD library 2.0.1
 * Tested on PHP 5.2.1
 *
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 * @copyright 2009 Andrei Bakulin. All rights reserved.
 * @link http://www.qpimg.com/
 * @version 0.6.0.beta
 */
class qpimg
{
    const MAP_CSS_SELECTOR_MASK             = 'qpimg_map_%MAP%';
    const OBJ_CSS_SELECTOR_MASK             = 'qpimg_obj_%MAP%_%OBJECT%';
    
    const CSS_FILE_KEY_DEFAULT              = 'std';
    const CSS_FILE_KEY_DATAURI_REJECT       = 'ie7mm';      // for browsers which not support data:URI

    const CSSGRP_FILE_KEY_DEFAULT           = 'std-GRP';
    const CSSGRP_FILE_KEY_DATAURI_REJECT    = 'ie7mm-GRP';  // for browsers which not support data:URI

    const SPRITE_FILE_KEY_DEFAULT           = 'std';
    const SPRITE_FILE_KEY_DATAURI_REJECT    = 'ie7mm';      // for browsers which not support data:URI
    
    /**
     * Generate string of css-file by incoming $map_id value.
     * If $hash value is null - it auto recount for current map.
     * If $hash value is not null - it attach to css-source URL without recount
     * 
     * @param string $map_id
     * 
     * @param string $hash
     * 
     * @return string css-source (URL)
     */
    static public function get_css_source( $map_id, $hash = null )
    {
        if( $hash === null )
        {
            $map_cfg = qpimg_config::get_map( $map_id );
    
            if( $map_cfg === false )
            {
                return false;
            }
            
            $hash = $map_cfg['hash'];
        }

        return qpimg_config::get_option('core_script_src') . "?obj=$map_id" . ( $hash ? ":$hash" : "" );
    }

    /**
     * Generate link to css file by incoming $map_id value.
     * 
     * @param string $map_id
     * 
     * @return string link to css file
     */
    static public function get_css_source_link( $map_id )
    {
        /*$map_cfg = qpimg_config::get_map( $map_id );
        
        if( $map_cfg === false )
        {
            return false;
        }*/
            
        return self::execute($map_id /*. ":" . $map_cfg['hash']*/, TRUE);
    }

    /**
     * Generate string to empty-image (look at config)
     * 
     * @return string empty-image source
     */
    static public function get_empty_image_src()
    {
        return qpimg_config::get_option('empty_image_src');
    }

    /**
     * Generate class values by incoming selector. Selector may be as:
     * - map_id.obj_id - generate "qpimg_map_class qpimg_obj_class"
     * - map_id        - generate only "qpimg_map_class"
     * - .obj_id       - try autodetect map_id and generate "qpimg_map_class qpimg_obj_class" 
     *                   or empty string (if map not found)
     * 
     * @param string $selector
     * 
     * @return string class-value ( "qpimg_map_class" or "qpimg_map_class qpimg_obj_class" )
     */
    static public function get_obj_class( $selector )
    {
        $selector = rtrim( trim( $selector ), '.' );

        $map_cfg = $map_id = $media_id = null;

        if( $selector )
        {
            list( $map_id, $media_id ) = explode( '.', $selector );

            if( $map_id == '' )
            {
                $map_id = null;

                $map_keys = qpimg_config::get_map_keys();
                
                foreach( $map_keys as $onemap_id )
                {
                    $onemap_cfg = qpimg_config::get_map( $onemap_id );
                    
                    if( isset( $onemap_cfg['objects'] ) === false )
                    {
                        continue;
                    }
    
                    if( isset( $onemap_cfg['objects'][$media_id] ) === true )
                    {
                        if( $map_id === null )
                        {
                            $map_id = $onemap_id;
                            $map_cfg = $onemap_cfg;
                        }
                        else
                        {
                            $map_cfg = $map_id = $media_id = null;
                            break;
                        }
                    }
                }
                // ToDo: maybe use here user-function
            }
            else
            {
                $map_cfg = qpimg_config::get_map( $map_id );
                
                if( $map_cfg === false )
                {
                    $map_cfg = $map_id = $media_id = null;
                }
            }
        }
        
        $classes = array();

        if( $map_id )
        {
            $classes[] = qpimg::get_map_css_selector( $map_id, $map_cfg );

            if( $media_id )
            {
                $classes[] = qpimg::get_obj_css_selector( $map_id, $media_id, $map_cfg );
            }
        }
        
        return join( ' ', $classes );
    }

    /**
     * Base generate function. Detect all using maps (incoming map_id & it's attach
     * and sub-sub-...-attach maps). Check validate of cache files. Then if need
     * generate data and make redirect to CSS-file. 
     * If $get_link is true then return path to file
     * 
     * @param string $map_id
     *
     * @param bool $get_link
     * 
     * @return bool global execute status 
     */
    static public function execute( $map_id, $get_link = FALSE )
    {
        list( $map_id ) = explode( ':', $map_id );

        $need_get_maps_stack = array( $map_id );

        $valid_maps_stack = array();
        $invalid_maps_stack = array();
        
        while( count( $need_get_maps_stack ) > 0 )
        {
            $one_map_id = array_shift( $need_get_maps_stack );

            $one_map_cfg = qpimg_config::get_map( $one_map_id );

            if( $one_map_cfg === false )
            {
                return false;
            }

            if( isset( $one_map_cfg['attach'] ) === true )
            {
                if( is_array( $one_map_cfg['attach'] ) === false )
                {
                    $one_map_cfg['attach'] = array( $one_map_cfg['attach'] );
                }
                
                foreach( $one_map_cfg['attach'] as $attach_map_id )
                {
                    if( isset( $valid_maps_stack[ $attach_map_id ] ) === false && isset( $invalid_maps_stack[ $attach_map_id ] ) === false )
                    {
                        $need_get_maps_stack[] = $attach_map_id;
                    }
                }
            }
            
            //---------------------------------------------------------------------
            // Check for data in cache

            if( qpimg_cache::validate( $one_map_cfg ) === true )
            {
                $valid_maps_stack[ $one_map_id ] = $one_map_cfg;
            }
            else
            {
                if( qpimg_cache::clean( $one_map_cfg ) === false )
                {
                    return false;
                }

                $invalid_maps_stack[ $one_map_id ] = $one_map_cfg;
            }
            
            if( $map_id == $one_map_id )
            {
                $map_cfg = $one_map_cfg;
            }
        }

        if( count( $invalid_maps_stack ) > 0 )
        {
            if( isset( $invalid_maps_stack[ $map_id ] ) === false )
            {
                $invalid_maps_stack[ $map_id ] = $valid_maps_stack[ $map_id ];
                unset( $valid_maps_stack[ $map_id ] );
            }

            //---------------------------------------------------------------------
            // Generate data
            
            foreach( $invalid_maps_stack as $one_map_id => $one_map_cfg )
            {
                if( qpimg_media::pack( $one_map_cfg ) === false )
                {
                    return false;
                }

                $valid_maps_stack[ $one_map_id ] = $invalid_maps_stack[ $one_map_id ];
            }

            qpimg_cache::clean( $one_map_cfg, true );
            
            if( qpimg_media::pack_grouper_css( $map_cfg, $valid_maps_stack ) === false )
            {
                return false;
            }

            //---------------------------------------------------------------------
        }

        return $get_link ? qpimg_cache::get_valid_css_source($map_cfg, isset($map_cfg['attach'])) : qpimg_cache::redirect( $map_cfg );
    }
    
    /**
     * Generate css-selector for map by map_id
     * 
     * @param string $map_id
     * 
     * @return string css-selector
     */
    static public function get_map_css_selector( $map_id, $map_cfg = null )
    {
        if( $map_cfg !== null && isset( $map_cfg['map_css_selector_mask'] ) === true )
        {
            $selector_mask = $map_cfg['map_css_selector_mask'];
        }
        else
        {
            $selector_mask = self::MAP_CSS_SELECTOR_MASK;
        }
        
        return str_replace( array( '%MAP%' ), array( $map_id ), $selector_mask );
    }

    /**
     * Generate css-selector for object of map by map_id & object_id
     * 
     * @param string $map_id
     * 
     * @param string $object_id
     * 
     * @return string css-selector
     */
    static public function get_obj_css_selector( $map_id, $object_id, $map_cfg = null )
    {
        if( $map_cfg !== null && isset( $map_cfg['obj_css_selector_mask'] ) === true )
        {
            $selector_mask = $map_cfg['obj_css_selector_mask'];
        }
        else
        {
            $selector_mask = self::OBJ_CSS_SELECTOR_MASK;
        }
        
        return str_replace( array( '%MAP%', '%OBJECT%' ), array( $map_id, $object_id ), $selector_mask );
    }
}
