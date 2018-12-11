<?php

/**
 * Class media_item_cfg  
 * @package qpimg
 */

/**
 * Class describe single media-item
 * Set default values (in construct) and safe-set user values
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class media_item_cfg
{
    private $attrs;

    /**
     * Constructor init default values
     * 
     * @param string $source - source of image
     * 
     * @param int $width - width of image
     * 
     * @param int $height - height of image
     * 
     * @param int $imagetype - image type (PHP const value)
     */
    public function __construct( $source, $width, $height, $imagetype ) 
    {
        $this->attrs = array();

        $this->attrs['source']          = $source;
        $this->attrs['etalon-width']    = $this->attrs['scale-width']  = $this->attrs['full-width']  = $width;
        $this->attrs['etalon-height']   = $this->attrs['scale-height'] = $this->attrs['full-height'] = $height;
        $this->attrs['imagetype']       = $imagetype;
        
        $this->attrs['left']            = 0;
        $this->attrs['top']             = 0;
        
        $this->attrs['scale-method']    = 'resize'; # { 'resize' | 'resample' | 'mosaic' }

        $this->attrs['space-top']       = 0;
        $this->attrs['space-right']     = 0;
        $this->attrs['space-bottom']    = 0;
        $this->attrs['space-left']      = 0;

        $this->attrs['bgcolor']         = false;
        
        $this->attrs['css-set:width']   = false;
        $this->attrs['css-set:height']  = false;
        $this->attrs['css-set:border']  = false;
        $this->attrs['css-set:bgcolor'] = false;

        $this->attrs['data:URI']        = false;

        $this->attrs['css-selector']    = array();
    }
    
    /**
     * Safe-set media-item value 
     * 
     * @param string $key
     * 
     * @param mix $value
     * 
     * @return bool - <true> if set; <false> if not set
     */
    public function set( $key, $value )
    {
        switch( $key )
        {
            case 'left':
            case 'top':
            {
                $this->attrs[ $key ] = (int) trim( str_replace( 'px', '', $value ) );
                break;
            }
            
            case 'space':
            {
                if( trim( $value ) == '' )
                {
                    return false;
                }

                $spaces = explode( ' ', $value );
                
                switch( count( $spaces ) )
                {
                    case 1:
                    {
                        $this->set( 'space-top',    $spaces[0] );
                        $this->set( 'space-right',  $spaces[0] );
                        $this->set( 'space-bottom', $spaces[0] );
                        $this->set( 'space-left',   $spaces[0] );
                        break;
                    }
                    
                    case 2:
                    {
                        $this->set( 'space-top',    $spaces[0] );
                        $this->set( 'space-right',  $spaces[1] );
                        $this->set( 'space-bottom', $spaces[0] );
                        $this->set( 'space-left',   $spaces[1] );
                        break;
                    }
                    
                    case 3:
                    {
                        $this->set( 'space-top',    $spaces[0] );
                        $this->set( 'space-right',  $spaces[1] );
                        $this->set( 'space-bottom', $spaces[2] );
                        $this->set( 'space-left',   $spaces[1] );
                        break;
                    }
                    
                    case 4:
                    default:
                    {
                        $this->set( 'space-top',    $spaces[0] );
                        $this->set( 'space-right',  $spaces[1] );
                        $this->set( 'space-bottom', $spaces[2] );
                        $this->set( 'space-left',   $spaces[3] );
                        break;
                    }
                }
                break;
            }

            case 'space-right':
            case 'space-left':
            case 'space-top':
            case 'space-bottom':
            {
                $this->attrs[ $key ] = (int) trim( str_replace( 'px', '', $value ) );
                break;
            }
            
            case 'scale':
            {
                if( trim( $value ) == '' )
                {
                    return false;
                }

                $scales = explode( ' ', $value );
                
                switch( count( $scales ) )
                {
                    case 1:
                    {
                        $this->set( 'scale-width',  $scales[0] );
                        $this->set( 'scale-height', $scales[0] );
                        break;
                    }
                    
                    case 2:
                    default:
                    {
                        $this->set( 'scale-width',  $scales[0] );
                        $this->set( 'scale-height', $scales[1] );
                        break;
                    }
                }
                break;
            }
            
            case 'scale-width':
            case 'scale-height':
            {
                if( strpos( $value, '%' ) !== false )
                {
                    $etalin_field = $key == 'scale-width' ? 'etalon-width' : 'etalon-height';

                    $value = substr( $value, 0, strpos( $value, '%' ) );
                    $this->attrs[ $key ] = round( $this->attrs[ $etalin_field ] * ( (float) $value ) / 100 );
                }
                else
                {
                    $this->attrs[ $key ] = (int) trim( str_replace( 'px', '', $value ) );
                }
                break;
            }

            case 'scale-method':
            {
                if( in_array( $value, array( 'resize', 'resample', 'mosaic' ) ) === false )
                {
                    return false;
                }
                
                $this->attrs[ $key ] = $value;
                break;
            }
            
            case 'bgcolor':
            {
                $this->attrs[ $key ] = $value;
                break;
            }

            case 'data:URI':
            case 'css-set:width':
            case 'css-set:height':
            {
                $this->attrs[ $key ] = (bool) $value;
                break;
            }

            case 'css-set:border':
            case 'css-set:bgcolor':
            {
                $this->attrs[ $key ] = $value;
                break;
            }

            case 'css-selector':
            {
                if( is_array( $value ) )
                {
                    $this->attrs[ $key ] = array_merge( $this->attrs[ $key ], $value );
                }
                else
                {
                    $this->attrs[ $key ][] = (string) $value;
                }
                break;
            }

            default:
            {
                return false; // all others -> readonly attributs
            }
        }

        $this->attrs['full-width']  = $this->attrs['space-left'] + $this->attrs['scale-width']  + $this->attrs['space-right'];
        $this->attrs['full-height'] = $this->attrs['space-top']  + $this->attrs['scale-height'] + $this->attrs['space-bottom'];

        return true;
    }
    
    /**
     * Return media-item value
     * 
     * @param string $key
     * 
     * @return mix - value if key exists; <false> if not exists
     */
    public function get( $key )
    {
        if( isset( $this->attrs[ $key ] ) === false )
        {
            return false;
        }
        
        return $this->attrs[ $key ];
    }
    
    /**
     * Function show is self-object is fallacious media-item.
     * This is not!  
     * 
     * @return false
     */
    public function is_crashed()
    {
        return false;
    }
}


/**
 * Class describe fallacious media-item
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class media_item_crash extends media_item_cfg
{
    /**
     * Constructor
     */
    public function __construct() 
    {
    }

    /**
     * Function show is self-object is fallacious media-item.
     * This is yes!  
     * 
     * @return true
     */
    public function is_crashed()
    {
        return true;
    }
}
