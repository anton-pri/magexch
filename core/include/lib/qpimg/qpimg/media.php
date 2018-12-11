<?php

/**
 * Class qpimg_media  
 * @package qpimg
 */

/**
 * Generator of media data (css & sprites)
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class qpimg_media
{
    const MODE_STANDARD         = 'std';
    const MODE_DATAURI_REJECT   = 'dataURI';

    /**
     * Save grouped-css-file from list of maps
     * 
     * @param array $base_map_cfg base map-config array
     * 
     * @param array $sub_map_cfgs list of map-config arrays 
     * 
     * @return bool status
     */
    static public function pack_grouper_css( $base_map_cfg, $sub_map_cfgs )
    {
        ksort( $sub_map_cfgs );

        foreach( array( qpimg::CSSGRP_FILE_KEY_DEFAULT, qpimg::CSSGRP_FILE_KEY_DATAURI_REJECT ) as $css_key )
        {
            $css_hf = @fopen( qpimg_cache::gen_filename( $base_map_cfg, 'css', $css_key ), 'w' );
            
            if( is_resource( $css_hf ) === false )
            {
                return false;
            }
            
            $filename_keys = array();
            
            switch( $css_key )
            {
                case qpimg::CSSGRP_FILE_KEY_DEFAULT:
                {
                    $filename_keys[] = qpimg::CSS_FILE_KEY_DEFAULT;
                    break;
                }
                
                case qpimg::CSSGRP_FILE_KEY_DATAURI_REJECT:
                {
                    $filename_keys[] = qpimg::CSS_FILE_KEY_DATAURI_REJECT;
                    $filename_keys[] = qpimg::CSS_FILE_KEY_DEFAULT;
                    break;
                }
            }

            foreach( $sub_map_cfgs as $one_map_id => $one_map_cfg )
            {
                foreach( $filename_keys as $one_css_key )
                {
                    $filename = qpimg_cache::gen_filename( $one_map_cfg, 'css', $one_css_key );
                    
                    if( file_exists( $filename ) === true )
                    {
                        break;
                    }

                    $filename = false;
                }

                if( $filename === false )
                {
                    continue; // but this cannot be :)
                }

                fwrite( $css_hf, "/* $one_map_id */\n" ); 
                fwrite( $css_hf, @file_get_contents( $filename ) );
            }
            
            fclose( $css_hf );
        }
        
        return true;
    }

    /**
     * Generate css & sprite (image) for single map
     * 
     * @param array $map_cfg map-config array
     * 
     * @return bool status 
     */
    static public function pack( $map_cfg )
    {
        //---------------------------------------------------------------------
        // PreCheck incoming data

        if( is_array( $map_cfg['objects'] ) === false )
        {
            $map_cfg['objects'] = array();
        }
        
        //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        
        if( isset( $map_cfg['verbose_check'] ) === false )
        {
            $map_cfg['verbose_check'] = false;
        }
        
        $map_cfg['verbose_check'] = (bool) $map_cfg['verbose_check'];

        //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        
        $map_cfg['save_format'] = strtolower( $map_cfg['save_format'] );
        
        if( in_array( $map_cfg['save_format'], array( 'png', 'gif', 'jpg' ) ) === false )
        {
            $map_cfg['save_format'] = 'png';
        }

        //---------------------------------------------------------------------

        self::prepare_media_items( $map_cfg );

        if( $map_cfg['objects:dataURI_files_count'] == 0 )
        {
            // if no dataURI media-items -> then no need proecee MODE_DATAURI_REJECT
            $process_modes = array( self::MODE_STANDARD );
        }
        else
        {
            $process_modes = array( self::MODE_STANDARD, self::MODE_DATAURI_REJECT );
        }
        
        foreach( $process_modes as $mode )
        {
            switch( $mode )
            {
                case self::MODE_STANDARD:
                {
                    $css_filename = qpimg_cache::gen_filename( $map_cfg, 'css', qpimg::CSS_FILE_KEY_DEFAULT );
                    $sprite_filename = qpimg_cache::gen_filename( $map_cfg, $map_cfg['save_format'], qpimg::SPRITE_FILE_KEY_DEFAULT );
                    $sprite_url = qpimg_cache::gen_filename( $map_cfg, $map_cfg['save_format'], qpimg::SPRITE_FILE_KEY_DEFAULT, true );
                    break;
                }

                case self::MODE_DATAURI_REJECT:
                {
                    $css_filename = qpimg_cache::gen_filename( $map_cfg, 'css', qpimg::CSS_FILE_KEY_DATAURI_REJECT );
                    $sprite_filename = qpimg_cache::gen_filename( $map_cfg, $map_cfg['save_format'], qpimg::SPRITE_FILE_KEY_DATAURI_REJECT );
                    $sprite_url = qpimg_cache::gen_filename( $map_cfg, $map_cfg['save_format'], qpimg::SPRITE_FILE_KEY_DATAURI_REJECT, true );
                    break;
                }
            }
            
            //---------------------------------------------------------------------
            // Set positions of each media-item-element for sprite

            switch( $map_cfg['orientation'] )
            {
                case 'static':
                default:
                {
                    $set_position_funcname = 'set_position_static';
                    $css_bg_repeat_style = 'no-repeat';
                    break;
                }

                case 'repeat_x':
                {
                    $set_position_funcname = 'set_position_repeat_x';
                    $css_bg_repeat_style = 'repeat-x';
                    break;
                }

                case 'repeat_y':
                {
                    $set_position_funcname = 'set_position_repeat_y';
                    $css_bg_repeat_style = 'repeat-y';
                    break;
                }
            }

            list( $pos_media_items, $img_width, $img_height ) = self::$set_position_funcname( $map_cfg, $mode );

            //---------------------------------------------------------------------
            // Save CSS data (by mode)

            $css_data = array();
            
            if( count( $pos_media_items ) > 0 )
            {
                $css_data[':main_sprite']['background-image'] = "url('$sprite_url')";
                $css_data[':main_sprite']['background-repeat'] = $css_bg_repeat_style;
                $css_data[':main_sprite'][':css-selector'] = array();
            }
            
            // Generate crash rules
            foreach( $map_cfg['objects:items'] as $media_id => $media_item )
            {
                if( $media_item->is_crashed() === false )
                {
                    continue;
                }
                
                $item_css_data = array();
                $item_css_data['background-image'] = 'none !important';
                $item_css_data['background-color'] = '#800000';

                $css_data[ qpimg::get_obj_css_selector( $map_cfg['id'], $media_id, $map_cfg ) ] = $item_css_data;
            }

            // Generate items data
            foreach( $pos_media_items as $media_id => $media_item )
            {
                $item_css_data = array();

                $item_css_data['background-position'] = 
                    -( $media_item->get('left') + $media_item->get('space-left') ) . "px " .
                    -( $media_item->get('top') + $media_item->get('space-top') ) . "px";

                self::prepare_item_css( $item_css_data, $media_item );

                $css_data[':main_sprite'][':css-selector'] = array_merge(
                    $css_data[':main_sprite'][':css-selector'],
                    $item_css_data[':css-selector']
                );
                
                $css_data[ qpimg::get_obj_css_selector( $map_cfg['id'], $media_id, $map_cfg ) ] = $item_css_data;
            }
            unset( $item_css_data );
            
            // Generate data:URI content (for MODE_STANDARD)
            if( $mode == self::MODE_STANDARD )
            {
                if( is_array( $map_cfg['objects:items'] ) === true )
                foreach( $map_cfg['objects:items'] as $media_id => $media_item )
                {
                    if( $media_item->is_crashed() === true )
                    {
                        continue;
                    }
                    
                    if( $media_item->get('data:URI') === false )
                    {
                        continue;
                    }

                    $item_css_data = array();
    
                    self::prepare_item_css( $item_css_data, $media_item );
                    
                    switch( $media_item->get('imagetype') )
                    {
                        case IMAGETYPE_GIF:  $mimetype = 'image/gif';  break;
                        case IMAGETYPE_JPEG: $mimetype = 'image/jpeg'; break;
                        case IMAGETYPE_PNG:  $mimetype = 'image/png';  break;
                    }

                    $item_css_data['background-image'] = "url(" .
                        "data:$mimetype;" .
                        "base64," . base64_encode( @file_get_contents( $media_item->get('source') ) ) . ")";
                    
                    $css_data[ qpimg::get_obj_css_selector( $map_cfg['id'], $media_id, $map_cfg ) ] = $item_css_data;
                }                
            }
            
            $css_hf = @fopen( $css_filename, 'w' );

            if( is_resource( $css_hf ) === false )
            {
                self::drop_temporary_media();
                return false;
            }
            
            ksort( $css_data );
            
            foreach( $css_data as $css_selector => $item_css_data )
            {
                if( $css_selector == ':main_sprite' )
                {
                    $css_selector = qpimg::get_map_css_selector( $map_cfg['id'], $map_cfg );
                }

                fwrite( $css_hf, ".$css_selector" );

                if( is_array( $item_css_data[':css-selector'] ) === true )
                foreach( $item_css_data[':css-selector'] as $sub_css_selector )
                {
                    $sub_css_selector = trim( $sub_css_selector );

                    if( $sub_css_selector == '' )
                    {
                        continue;
                    }

                    fwrite( $css_hf, ", {$sub_css_selector}" );
                }

                fwrite( $css_hf, " { " );
    
                foreach( $item_css_data as $css_attr_key => $css_attr_value )
                {
                    if( $css_attr_key{0} == ':' )
                    {
                        continue;
                    }

                    fwrite( $css_hf, "$css_attr_key: $css_attr_value; " );
                }
                
                fwrite( $css_hf, "}\n" );
            }

            fclose( $css_hf );

            //---------------------------------------------------------------------
            // Make SPRITE (image)
            
            if( count( $pos_media_items ) > 0 )
            {
                $sprite_img = @imagecreatetruecolor( $img_width, $img_height );
        
                if( is_resource( $sprite_img ) === false )
                {
                    self::drop_temporary_media();
                    return false;
                }

                imagealphablending( $sprite_img, false );
                imagesavealpha( $sprite_img, true );
        
                if( isset( $map_cfg['bgcolor'] ) === true && $map_cfg['bgcolor'] )
                {
                    list( $color_r, $color_g, $color_b ) = qpimg_utils::color2rgb( $map_cfg['bgcolor'] );
    
                    imagefill( $sprite_img, 0, 0, imagecolorallocate( $sprite_img, $color_r, $color_g, $color_b ) );
                    
                    unset( $color_r, $color_g, $color_b );
                }
                else
                {
                    imagefill( $sprite_img, 0, 0, imagecolorallocatealpha( $sprite_img, 0, 0, 0, 127 ) );
                }
                
                //---------------------------------------------------------------------

                if( isset( $map_cfg['transparent_color'] ) === true )
                {
                    list( $color_r, $color_g, $color_b ) = qpimg_utils::color2rgb( $map_cfg['transparent_color'] );
    
                    imagecolortransparent( $sprite_img, 
                        imagecolorallocate( $sprite_img, $color_r, $color_g, $color_b )
                    );

                    unset( $color_r, $color_g, $color_b );
                }

                //---------------------------------------------------------------------
                // Copy images in sprite & save css attributes for each element
                
                foreach( $pos_media_items as $media_id => $media_item )
                {
                    $safe_image_source = self::parse_media_source( $media_item->get('source'), $map_cfg['id'] );
                    
                    if( $safe_image_source === false )
                    {
                        continue;
                    }

                    //- - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                    
                    switch( $media_item->get('imagetype') )
                    {
                        case IMAGETYPE_GIF:
                        {
                            $tmp_img = @imagecreatefromgif( $safe_image_source );
                            break;
                        }
                
                        case IMAGETYPE_JPEG:
                        {
                            $tmp_img = @imagecreatefromjpeg( $safe_image_source );
                            break;
                        }
                
                        case IMAGETYPE_PNG:
                        {
                            $tmp_img = @imagecreatefrompng( $safe_image_source );
                            break;
                        }
                    }
                    
                    if( is_resource( $tmp_img ) === false )
                    {
                        continue;
                    }
                    
                    $src_x = 0;
                    $src_y = 0;
                    $src_w = $media_item->get('etalon-width');
                    $src_h = $media_item->get('etalon-height');
                    
                    $dst_x = $media_item->get('left') + $media_item->get('space-left');
                    $dst_y = $media_item->get('top') + $media_item->get('space-top');
                    $dst_w = $media_item->get('scale-width');
                    $dst_h = $media_item->get('scale-height');
        
                    if( $media_item->get('bgcolor') !== false )
                    {
                        list( $color_r, $color_g, $color_b ) = qpimg_utils::color2rgb( $media_item->get('bgcolor') );
        
                        imagefilledrectangle(
                            $sprite_img,
                            $media_item->get('left'),
                            $media_item->get('top'),
                            $media_item->get('left') + $media_item->get('full-width') - 1,
                            $media_item->get('top') + $media_item->get('full-height') - 1,
                            imagecolorallocate( $sprite_img, $color_r, $color_g, $color_b )
                        );
        
                        unset( $color_r, $color_g, $color_b );
                    }
        
                    if( $src_w == $dst_w && $src_h == $dst_h )
                    {
                        imagecopy(
                            $sprite_img,    // copy to
                            $tmp_img,       // copy from
                            $dst_x, $dst_y,
                            $src_x, $src_y,
                            $src_w, $src_h
                        );
                    }
                    else
                    {
                        if( $media_item->get('scale-method') == 'resize' )
                        {
                            imagecopyresized(
                                $sprite_img,    // copy to
                                $tmp_img,       // copy from
                                $dst_x, $dst_y,
                                $src_x, $src_y,
                                $dst_w, $dst_h,
                                $src_w, $src_h
                            );                    
                        }
                        elseif( $media_item->get('scale-method') == 'resample' )
                        {
                            imagecopyresampled(
                                $sprite_img,    // copy to
                                $tmp_img,       // copy from
                                $dst_x, $dst_y,
                                $src_x, $src_y,
                                $dst_w, $dst_h,
                                $src_w, $src_h
                            );                    
                        }
                        elseif( $media_item->get('scale-method') == 'mosaic' )
                        {
                            $mosaic_y = $dst_y;
                            $mosaic_h = $dst_h;
                            
                            do
                            {
                                $mosaic_x = $dst_x;
                                $mosaic_w = $dst_w;
                            
                                do
                                {
                                    imagecopy(
                                        $sprite_img,    // copy to
                                        $tmp_img,       // copy from
                                        $mosaic_x, $mosaic_y,
                                        0, 0,
                                        min( $mosaic_w, $src_w ), min( $mosaic_h, $src_h )
                                    );
                                    
                                    $mosaic_x += $src_w;
                                    $mosaic_w -= $src_w;
                                }
                                while( $mosaic_w > 0 );
                                
                                $mosaic_y += $src_h;
                                $mosaic_h -= $src_h;
                            }
                            while( $mosaic_h > 0 );
                            
                            unset( $mosaic_x, $mosaic_y, $mosaic_w, $mosaic_h );
                        }
                        else
                        {
                            qpimg_logger::write( "QPIMG_WARNING: Undefined scale-method: " . $media_item->get('scale-method'), __FILE__, __LINE__ );
                            continue;
                        }
                    }
        
                    unset( $src_x, $src_y, $src_w, $src_h );
                    unset( $dst_x, $dst_y, $dst_w, $dst_h );
        
                    imagedestroy( $tmp_img );
                }
        
                //---------------------------------------------------------------------
                // Save image-data
    
                switch( $map_cfg['save_format'] )
                {
                    case 'png':
                    {
                        if( isset( $map_cfg['save_quality'] ) === false )
                        {
                            $map_cfg['save_quality'] = 9;
                        }
    
                        $save_result = @imagepng( $sprite_img, $sprite_filename, min( max( $map_cfg['save_quality'], 0 ), 9 ) );
                        break;
                    }
                    
                    case 'gif':
                    {
                        $save_result = @imagegif( $sprite_img, $sprite_filename );
                        break;
                    }
                    
                    case 'jpg':
                    {
                        if( isset( $map_cfg['save_quality'] ) === false )
                        {
                            $map_cfg['save_quality'] = 95;
                        }
    
                        $save_result = @imagejpeg( $sprite_img, $sprite_filename, min( max( $map_cfg['save_quality'], 0 ), 100 ) );
                        break;
                    }
                }
                
                if( $save_result === false )
                {
                    qpimg_logger::write( "QPIMG_ERROR: Failed on save result sprite '$sprite_filename'", __FILE__, __LINE__ );
                }
        
                imagedestroy( $sprite_img );
            }
        }

        //---------------------------------------------------------------------
        // Save crc-hash [if set verbose_check mode]
        
        if( $map_cfg['verbose_check'] === true )
        {
            @file_put_contents(
                qpimg_cache::gen_filename( $map_cfg, 'crc' ),
                $map_cfg['hash'] . ':' . qpimg_cache::get_media_hash( $map_cfg )
            );
        }

        self::drop_temporary_media();
        return true;
    }
    
    /**
     * Parse base key & values of media-item
     * 
     * @param string $media_id
     * 
     * @param array $media_cfg
     * 
     * @return array( $media_id, $presets, $media_source, $media_cfg )
     *      $media_id - media_id value
     *      $presets - array( 'set' => array(..), 'unset' => array(..) ) of presets
     *      $media_source - source of media image
     *      $media_cfg - array of media options
     */
    static public function parse_media_values( $media_id, $media_cfg )
    {
        if( strpos( $media_id, '@' ) !== false )
        {
            list( $media_id, $presets_id ) = explode( '@', $media_id, 2 );
            $presets_id = explode( '@', $presets_id );
        }
        else
        {
            $presets_id = array();
        }
        
        if( is_array( $media_cfg ) === true )
        {
            $media_source = $media_cfg['source'];
            unset( $media_cfg['source'] );
        }
        else
        {
            $media_source = $media_cfg;
            $media_cfg = array();
        }

        if( isset( $media_cfg['presets'] ) )
        {
            $presets_id = array_merge( $presets_id, explode( '@', $media_cfg['presets'] ) );
        }

        $presets = array();
        $presets['set'] = array();
        $presets['unset'] = array();

        foreach( $presets_id as $preset_id )
        {
            $preset_id = trim( $preset_id );
            
            if( $preset_id == '' )
            {
                continue;
            }

            if( $preset_id{0} == '~' )
            {
                $presets['unset'][] = ltrim( $preset_id, '~' );
            }
            else
            {
                $presets['set'][] = $preset_id;
            }
        }

        return array( $media_id, $presets, $media_source, $media_cfg );
    }
    
    /**
     * Prepare media-items array by map-config array. 
     * Make media-items objects & detect count of data:URI objects.
     * 
     * @param array & $map_cfg map-config array (save result in it)
     * 
     * @return bool true
     */
    static private function prepare_media_items( & $map_cfg )
    {
        $media_items = array();
        $dataURI_files_count = 0;

        //----------------------------------------------------------------------
        // Prepare map-default presets array

        $default_presets = array( '@' );

        if( isset( $map_cfg['default_presets'] ) === true )
        {
            $tmp_default_presets = explode( '@', $map_cfg['default_presets'] );

            foreach( $tmp_default_presets as $tmp_default_preset )
            {
                if( $tmp_default_preset == '' || $tmp_default_preset{0} == '~' )
                {
                    continue;
                }

                $default_presets[] = $tmp_default_preset;
            }
            unset( $tmp_default_presets, $tmp_default_preset );
        }

        //----------------------------------------------------------------------

        foreach( $map_cfg['objects'] as $media_id => $media_cfg )
        {
            list( $media_id, $presets, $media_source, $media_cfg ) = self::parse_media_values( $media_id, $media_cfg );
            
            $safe_media_source = self::parse_media_source( $media_source, $map_cfg['id'] );
            
            if( $safe_media_source === false )
            {
                $media_items[ $media_id ] = new media_item_crash();
                
                qpimg_logger::write( "QPIMG_NOTICE: Failed on get file '$media_source' (map_id - '{$map_cfg['id']}'; media_id - '{$media_id}')", __FILE__, __LINE__ );
                continue;
            }

            $img_attrs = getimagesize( $safe_media_source );

            if( $img_attrs === false )
            {
                $media_items[ $media_id ] = new media_item_crash();
                qpimg_logger::write( "QPIMG_NOTICE: Failed on get image size '$media_source' (map_id - '{$map_cfg['id']}'; media_id - '{$media_id}')", __FILE__, __LINE__ );
                continue;
            }

            list( $img_width, $img_height, $img_imagetype ) = $img_attrs;
            
            $media_item = new media_item_cfg( $media_source, $img_width, $img_height, $img_imagetype );
            
            //-----------------------------------------------------------------
            
            if( filesize( $safe_media_source ) > qpimg_config::get_option('dataURI_filesize_limit') )
            {
                $media_item->set( 'data:URI', false );
            }
            elseif( $map_cfg['data:URI'] === true )
            {
                $media_item->set( 'data:URI', true );
            }

            //---[1] Apply presets config attributes---

            $set_presets = array_merge( $default_presets, $presets['set'] );

            foreach( $set_presets as $preset_id )
            {
                $preset_id = trim( $preset_id );

                if( $preset_id == '' )
                {
                    continue;
                }
                if( in_array( $preset_id, $presets['unset'] ) === true )
                {
                    continue;
                }

                $preset_attrs = qpimg_config::get_preset( $preset_id );

                if( $preset_attrs === false )
                {
                    qpimg_logger::write( "QPIMG_NOTICE: Undefined preset id '{$preset_id}' (map_id - '{$map_cfg['id']}'; media_id - '{$media_id}')", __FILE__, __LINE__ );
                    continue;
                }

                foreach( $preset_attrs as $cfg_key => $cfg_value )
                {
                    $media_item->set( $cfg_key, $cfg_value );
                }
            }

            //---[2] Apply own config attributes---
            
            foreach( $media_cfg as $cfg_key => $cfg_value )
            {
                $media_item->set( $cfg_key, $cfg_value );
            }

            if( $media_item->get('data:URI') === true )
            {
                $dataURI_files_count++;
            }

            $media_items[ $media_id ] = $media_item;
        }

        $map_cfg['objects:items'] = $media_items;
        $map_cfg['objects:dataURI_files_count'] = $dataURI_files_count;

        return true;
    }
    
    /**
     * Base prepare css values
     * 
     * @param array & $item_css_data css-array values
     * 
     * @param array $media_item array of media-item options
     * 
     * @return bool true 
     */
    static private function prepare_item_css( & $item_css_data, $media_item )
    {
        if( $media_item->get('css-set:width') !== false )
        {
            $item_css_data['width'] = $media_item->get('scale-width') . "px";
        }

        if( $media_item->get('css-set:height') !== false )
        {
            $item_css_data['height'] = $media_item->get('scale-height') . "px";
        }

        if( $media_item->get('css-set:border') !== false )
        {
            $item_css_data['border'] = $media_item->get('css-set:border');
        }

        if( $media_item->get('css-set:bgcolor') !== false )
        {
            $item_css_data['background-color'] = $media_item->get('css-set:bgcolor');
        }

        $item_css_data[':css-selector'] = $media_item->get('css-selector');
        
        return true;
    }
    
    /**
     * Filter array of media-items and generate new media-items array by mode
     * and ignore crash-config-items
     * 
     * @param array $media_items incoming array of media-items
     * 
     * @param const $mode filter-mode
     * 
     * @return array new filtered array of media-items
     */
    static private function filter_media_items( $media_items, $mode )
    {
        $result_items = array();

        if( is_array( $media_items ) === true )
        foreach( $media_items as $media_id => $media_item )
        {
            if( $media_item->is_crashed() === true )
            {
                continue;
            }

            if( $mode == self::MODE_STANDARD )
            if( $media_item->get('data:URI') === true )
            {
                continue;
            }

            $result_items[ $media_id ] = $media_item;
        }

        return $result_items;
    }

    /**
     * Return max width and height values of incoming array of media-items 
     * 
     * @param array $fmedia_items incoming array of media-items
     * 
     * @return array( max-width, max-height )  
     */
    static private function get_max_sizes( $fmedia_items )
    {
        $max_img_width = 0;
        $max_img_height = 0;
        
        foreach( $fmedia_items as $media_id => $media_item )
        {
            $max_img_width  = max( $max_img_width,  $media_item->get('full-width') );
            $max_img_height = max( $max_img_height, $media_item->get('full-height') );
        }

        return array( $max_img_width, $max_img_height );
    }

    /**
     * Set position of each media-item on result sprite for orientation mode = 'static'
     * Almost count sprite-width & sprite-height.
     * 
     * @param array $map_cfg map-config array
     * 
     * @param const $mode generate-mode
     * 
     * @return array( media-items array, sprite-width, sprite-height ) 
     */
    static private function set_position_static( $map_cfg, $mode )
    {
        $fmedia_items = self::filter_media_items( $map_cfg['objects:items'], $mode );
        
        if( count( $fmedia_items ) == 0 )
        {
            return array( array(), 0, 0 );
        }

        list( $max_img_width, $max_img_height ) = self::get_max_sizes( $fmedia_items );

        //---------------------------------------------------------------------

        if( isset( $map_cfg['map_width'] ) === true && is_numeric( $map_cfg['map_width'] ) === true )
        {
            $want_map_width = (int) $map_cfg['map_width'];
        }
        else
        {
            $want_map_width = 1000;
        }

        //---------------------------------------------------------------------
        // Generate additional attrs for each image to set position in 
        // result sprite

        $result_media_items = array();
        $sprite_img_width   = max( $max_img_width, $want_map_width );  // max image width (will be minimized letter)
        $sprite_img_height  = 0;                                       // bottom used point
        $max_used_width     = 0;

        uasort( $fmedia_items, array( 'qpimg_media', 'objects_sort_iteration' ) );
        
        $free_regions = array(); // struct of item is: array with keys { top | left | width | height }
            
        while( count( $fmedia_items ) > 0 ) 
        {
            $detected_media_item_key  = null;
            $detected_free_region_key = null;

            usort( $free_regions, array( 'qpimg_media', 'free_regions_sort_iteration' ) );

            foreach( $free_regions as $free_region_key => $free_region )
            {
                foreach( $fmedia_items as $media_item_key => $media_item )
                {
                    if( $media_item->get('full-width') <= $free_region['width'] && 
                        $media_item->get('full-height') <= $free_region['height'] )
                    {
                        $detected_media_item_key  = $media_item_key;
                        $detected_free_region_key = $free_region_key;
                        break;
                    }
                }
            }
            
            if( $detected_free_region_key !== null )
            {
                // [free region & media-item] pair is detected

                $media_item_key = $detected_media_item_key; // alias :)
                $media_item     = $fmedia_items[ $media_item_key ];

                unset( $fmedia_items[ $media_item_key ] );

                //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                
                $free_region = $free_regions[ $detected_free_region_key ];

                unset( $free_regions[ $detected_free_region_key ] );

                //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                /*
                 *  +-------+---------------+ // global rectangle -> is free_region (detected)
                 *  |IMG....|free-region-1  | // IMG              -> media item (detected)
                 *  |.......|               | // free-region-1    -> new free region
                 *  +-------+---------------+ // free-region-2    -> new free region
                 *  |free-region-2          |
                 *  |                       |
                 *  +-----------------------+
                 */

                $media_item->set( 'left', $free_region['left'] );
                $media_item->set( 'top',  $free_region['top'] );
                $result_media_items[ $media_item_key ] = $media_item;

                
                $free_regions[] = array( // 1
                    'left'   => $free_region['left'] + $media_item->get('full-width'),
                    'top'    => $free_region['top'],
                    'width'  => $free_region['width'] - $media_item->get('full-width'),
                    'height' => $media_item->get( 'full-height' ),
                );

                $free_regions[] = array( // 2
                    'left'   => $free_region['left'],
                    'top'    => $free_region['top'] + $media_item->get('full-height'),
                    'width'  => $free_region['width'],
                    'height' => $free_region['height'] - $media_item->get('full-height'),
                );

                $max_used_width = max( $max_used_width, $free_region['left'] + $media_item->get('full-width') );
            }
            else
            {
                // [free region & media-item] pair is NOT detected
                // For 1st $media_item in array -> set image position from new line (from beggining).
                // Add new free-region.
                
                reset( $fmedia_items );

                $media_item_key = key( $fmedia_items );
                $media_item     = current( $fmedia_items );
                
                unset( $fmedia_items[ $media_item_key ] );

                //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                /*
                 *  +-------+---------------+ // IMG              -> media item (1st in array) 
                 *  |IMG....|free-region    | // free-region      -> new free region
                 *  |.......|               |
                 *  +-------+---------------+
                 */

                $media_item->set( 'left', 0 );
                $media_item->set( 'top', $sprite_img_height );
                $result_media_items[ $media_item_key ] = $media_item;


                $free_regions[] = array(
                    'left'   => $media_item->get('full-width'),
                    'top'    => $sprite_img_height,
                    'width'  => $sprite_img_width - $media_item->get('full-width'),
                    'height' => $media_item->get('full-height'),
                );

                $sprite_img_height += $media_item->get('full-height');
                
                $max_used_width = max( $max_used_width, $media_item->get('full-width') );
            }
        }
        
        $sprite_img_width = min( $max_used_width, $sprite_img_width );

        return array( $result_media_items, $sprite_img_width, $sprite_img_height );
    }

    /**
     * Set position of each media-item on result sprite for orientation mode = 'repeat_x'
     * Almost count sprite-width & sprite-height.
     * 
     * @param array $map_cfg map-config array
     * 
     * @param const $mode generate-mode
     * 
     * @return array( media-items array, sprite-width, sprite-height ) 
     */
    static private function set_position_repeat_x( $map_cfg, $mode )
    {
        $fmedia_items = self::filter_media_items( $map_cfg['objects:items'], $mode );

        if( count( $fmedia_items ) == 0 )
        {
            return array( array(), 0, 0 );
        }

        list( $max_img_width, $max_img_height ) = self::get_max_sizes( $fmedia_items );

        //---------------------------------------------------------------------
        
        if( isset( $map_cfg['map_width'] ) === true && is_numeric( $map_cfg['map_width'] ) === true )
        {
            $map_cfg['map_width'] = max( (int) $map_cfg['map_width'], $max_img_width );
        }
        else
        {
            $map_cfg['map_width'] = $max_img_width;
        }

        if( ! ( $map_cfg['map_width'] > 0 && $map_cfg['map_width'] <= 10000 ) )
        {
            qpimg_logger::write( "QPIMG_NOTICE: Wrong width value '{$map_cfg['map_width']}' for repeat-x-orientation map '{$map_cfg['id']}'", __FILE__, __LINE__ );
            $map_cfg['map_width'] = 10000;
        }
        
        //---------------------------------------------------------------------
        
        $sprite_img_width   = $map_cfg['map_width'];
        $sprite_img_height  = 0;
        
        $result_media_items = array();

        foreach( $fmedia_items as $media_item_key => $media_item )
        {
            $media_item->set( 'left', 0 );
            $media_item->set( 'top', $sprite_img_height );
            
            $media_item->set( 'space-left', 0 );
            $media_item->set( 'space-right', 0 );

            $media_item->set( 'scale-width', $sprite_img_width );
            
            if( $media_item->get( 'etalon-width' ) == 1 )
            {
                $media_item->set( 'scale-method', 'resize' );
            }
            else
            {
                $media_item->set( 'scale-method', 'mosaic' );
            }
            
            $sprite_img_height += $media_item->get('full-height');
            
            $result_media_items[ $media_item_key ] = $media_item;
        }        

        return array( $result_media_items, $sprite_img_width, $sprite_img_height );
    }

    /**
     * Set position of each media-item on result sprite for orientation mode = 'repeat_y'
     * Almost count sprite-width & sprite-height.
     * 
     * @param array $map_cfg map-config array
     * 
     * @param const $mode generate-mode
     * 
     * @return array( media-items array, sprite-width, sprite-height ) 
     */
    static private function set_position_repeat_y( $map_cfg, $mode )
    {
        $fmedia_items = self::filter_media_items( $map_cfg['objects:items'], $mode );

        if( count( $fmedia_items ) == 0 )
        {
            return array( array(), 0, 0 );
        }

        list( $max_img_width, $max_img_height ) = self::get_max_sizes( $fmedia_items );

        //---------------------------------------------------------------------
        
        if( isset( $map_cfg['map_height'] ) === true && is_numeric( $map_cfg['map_height'] ) === true )
        {
            $map_cfg['map_height'] = max( (int) $map_cfg['map_height'], $max_img_height );
        }
        else
        {
            $map_cfg['map_height'] = $max_img_height;
        }

        if( ! ( $map_cfg['map_height'] > 0 && $map_cfg['map_height'] <= 10000 ) )
        {
            qpimg_logger::write( "E_USER_NOTICE: Wrong height value '{$map_cfg['map_height']}' for repeat-y-orientation map '{$map_cfg['id']}'", __FILE__, __LINE__ );
            $map_cfg['map_width'] = 10000;
        }
        
        //---------------------------------------------------------------------

        $sprite_img_width   = 0;
        $sprite_img_height  = $map_cfg['map_height'];

        $result_media_items = array();

        foreach( $fmedia_items as $media_item_key => $media_item )
        {
            $media_item->set( 'left', $sprite_img_width );
            $media_item->set( 'top', 0 );
            
            $media_item->set( 'space-top', 0 );
            $media_item->set( 'space-bottom', 0 );

            $media_item->set( 'scale-height', $sprite_img_height );
            
            if( $media_item->get( 'etalon-height' ) == 1 )
            {
                $media_item->set( 'scale-method', 'resize' );
            }
            else
            {
                $media_item->set( 'scale-method', 'mosaic' );
            }
            
            $sprite_img_width += $media_item->get('full-width');
            
            $result_media_items[ $media_item_key ] = $media_item;
        }        

        return array( $result_media_items, $sprite_img_width, $sprite_img_height );
    }

    /**
     * Array for save temporary pathes of images 
     */
    static private $temporary_media = array();
    
    /**
     * Prepare media_source. Detect, if media_source is URL then automatically
     * download image to temporary folder and change source to temporary image.
     * All temporary pathes to images save in array for cleaning in the end of 
     * script exection.
     * 
     * If file is local-on-server then check if file exists
     * 
     * @param string $media_source
     * 
     * @return string new media_source
     */
    static private function parse_media_source( $media_source, $map_id )
    {
        if( strpos( $media_source, 'http://' ) !== false )
        {
            if( isset( self::$temporary_media[ $map_id ][ $media_source ] ) === true )
            {
                return self::$temporary_media[ $map_id ][ $media_source ];
            }

            $media_content = @file_get_contents( $media_source );
            
            if( $media_content === false )
            {
                return false;
            }
            
            $tmpname = tempnam( null, 'qpi' );
            
            @file_put_contents( $tmpname, $media_content );
            
            self::$temporary_media[ $map_id ][ $media_source ] = $tmpname;
            
            return self::$temporary_media[ $map_id ][ $media_source ];
        }

        if( file_exists( $media_source ) === false )
        {
            return false;
        }
        
        return $media_source;
    }

    /**
     * Clean all temporary images
     * 
     * @return bool true
     */
    static private function drop_temporary_media()
    {
        foreach( self::$temporary_media as $map_id => $source )
        {
	        foreach( $source as $media_source => $tmpname )
	        {
	            @unlink( $tmpname );
	        }
        }
        
        return true;
    }

    /**
     * Sort function sort two media-items by height & width values.
     * Biggest value must be first in array.
     * 
     * @param object $media_item1
     * 
     * @param object $media_item2
     * 
     * @return int { -1 | 0 | 1 }
     */
    static public function objects_sort_iteration( $media_item1, $media_item2 )
    {
        if( $media_item1->get('full-height') == $media_item2->get('full-height') )
        {
            if( $media_item1->get('full-width') == $media_item2->get('full-width') )
            {
                return 0;
            }
            
            return $media_item1->get('full-width') > $media_item2->get('full-width') ? -1 : 1;
        }
        
        return $media_item1->get('full-height') > $media_item2->get('full-height') ? -1 : 1;
    }

    /**
     * Sort function sort two free-region items by area
     * Biggest value must be first in array.
     * 
     * @param array $free_region_item1
     * 
     * @param array $free_region_item2
     * 
     * @return int { -1 | 0 | 1 }
     */
    static function free_regions_sort_iteration( $free_region_item1, $free_region_item2 )
    {
        $area1 = $free_region_item1['width'] * $free_region_item1['height'];
        $area2 = $free_region_item2['width'] * $free_region_item2['height'];
        
        if( $area1 == $area2 )
        {
            return 0;
        }
        
        return $area1 > $area2 ? -1 : 1;
    }
}
