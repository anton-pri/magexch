<?php

  global $app_main_dir, $errorManager;

  require_once $app_main_dir.'/include/lib/error/CW_Error.class.php';
  require_once $app_main_dir.'/include/lib/error/ErrorCollector.class.php';


  $errorManager   =& ErrorCollector::instance();

  // ------------------------------------------------------------
  //  Error related functions
  // ------------------------------------------------------------

    /*
     * create a new error
     */

    function error($message, $additional_params = array(), $is_fatal=null) {
        $error = new CW_Error($message, $additional_params, $is_fatal);
        return $error;
    }

  /**
   * Check if specific variable is error object
   *
   * @param mixed $var Variable that need to be checked
   * @return boolean
   */
  function is_error($var=null) {
    global $errorManager;
    if (is_null($var)) return $errorManager->hasErrors();
    return instance_of($var, 'CW_Error');
  } // is_error

  /**
   * Show nice error output.
   *
   * @param Error $error
   * @param boolean $die Die when done, default value is true
   * @return null
   */
  function dump_error($error, $die = true) {
    static $css_rendered = false;
    global $app_main_dir;

    if(!instance_of($error, 'CW_Error')) {
      print '$error is not valid <i>CW_Error</i> instance!';
      return;
    } // if

    include  $app_main_dir. '/include/lib/error/dump_error.php';

    if($die) {
      die();
    } // if
  } // dump_error

    function get_error_message() {
        global $errorManager;
        $errors = $errorManager->getErrors();
        $msg = array();
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $msg[] = $error->getMessage();
            }
        }
        return join("\n",$msg);
    } 
