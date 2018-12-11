<?php

/**
 * Class qpimg_utils  
 * @package qpimg
 */

/**
 * Useful functions for qpimg-project
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class qpimg_utils
{
    /**
     * Return $_POST or $_GET var value by key 
     * 
     * @param string $varname asked varname
     * 
     * @param mix $default this value return if asked var not exists
     *      in $_POST or $_GET arrays
     * 
     * @return mix asked value 
     */
    static public function get_var( $varname, $default = false )
    {
        if( isset( $_POST[ $varname ] ) === true )
        {
            $value = $_POST[ $varname ];
        }
        elseif( isset( $_GET[ $varname ] ) === true )
        {
            $value = $_GET[ $varname ];
        }
        else
        {
            return $default;
        }

        if( get_magic_quotes_gpc() )
        {
            $value = self::get_var__stripslashes( $value );
        }

        return $value;
    }
    
    /**
     * Private function which strip slashes of imconing var-value
     * 
     * @param mix $array incomin value(s) for strip
     * 
     * @return mix striped value(s) 
     */
    static private function get_var__stripslashes( $array )
    {
        return is_array( $array ) === true
            ? array_map( array( $this, __FUNCTION__ ), $array )
            : stripslashes( $array );
    }

    /**
     * Detect is current browser Internet Explorer 7 or lower
     * 
     * @return bool 
     */
    static public function check_is_IE7mm()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $matches = array();

        if( preg_match( '/MSIE ([0-9]\.[0-9])/', $user_agent, $matches ) == 0 )
        {
            return false;
        }
        
        if( strpos( $user_agent, 'Opera' ) !== false )
        {
            return false;
        }

        return (float) $matches[1] >= 8 ? false : true;
    }

    /**
     * Send output headers
     * 
     * @return true
     */
    static public function send_headers( $headers )
    {
        if( isset( $headers[':Response-Code'] ) === true )
        {
            header( "HTTP/1.0 {$headers[':Response-Code']} Not Modified" );
            unset( $headers[':Response-Code'] );
        }

        if( is_array( $headers ) === true )
        foreach( $headers as $key => $value )
        {
            header( "$key: $value" );
        }
        
        return true;
    }

    /**
     * Convert color string (#RRGGBB) to array of Red+Green+Blue decimal values
     * 
     * @param string $color (in format #RRGGBB or #RGB)
     * 
     * @return array( reg, green, blue )
     */
    static public function color2rgb( $color )
    {
        $color = ltrim( $color, '#' );

        switch( strlen( $color ) )
        {
            case 3:
            {
                $color_r = hexdec( substr( $color, 0, 1 ) ); $color_r = $color_r * 16 + $color_r;
                $color_g = hexdec( substr( $color, 1, 1 ) ); $color_g = $color_g * 16 + $color_g;
                $color_b = hexdec( substr( $color, 2, 1 ) ); $color_b = $color_b * 16 + $color_b;
                break;
            }
            
            case 6:
            {
                $color_r = hexdec( substr( $color, 0, 2 ) );
                $color_g = hexdec( substr( $color, 2, 2 ) );
                $color_b = hexdec( substr( $color, 4, 2 ) );
                break;
            }
            
            default:
            {
                $color_r = 0;
                $color_g = 0;
                $color_b = 0;
                break;
            }
        }

        return array( $color_r, $color_g, $color_b );
    }
}
