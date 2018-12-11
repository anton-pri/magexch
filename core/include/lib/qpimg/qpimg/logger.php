<?php

/**
 * Class qpimg_logger
 * @package qpimg
 */

/**
 * Logger class
 * 
 * @package qpimg
 * @author Andrei Bakulin <andrei.bakulin@gmail.com>
 */
class qpimg_logger
{
    /**
     * Function log any own error
     */
    static public function write( $message, $err_file, $err_line )
    {
        $err_message = "[" . date('r') . "] $message ($err_file:$err_line)\n";

        if( qpimg_config::get_option('log_file') )
        {
            if( @file_put_contents( qpimg_config::get_option('log_file'), $err_message, FILE_APPEND ) )
            {
                return true;
            }
        }

        print "<div><strong>qpimg message:</strong><br />$err_message</div>";

        return true;
    }
}
