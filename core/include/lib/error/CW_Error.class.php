<?php

  /**
   * Error class
   *
   * Errors are similar to exceptions in PHP5 but without some cool tricks
   * that build in error handling provides.
   */
  class CW_Error {

    /**
     * Error message
     *
     * @var string
     */
    var $message;

    /**
     * Error line
     *
     * @var integer
     */
    var $line;

    /**
     * Error file
     *
     * @var string
     */
    var $file;

    /**
     * Backtrace array
     *
     * @var array
     */
    var $backtrace;

    /**
     * Fatal errors are treated as exceptions
     *
     * On fatal error ErrorCollector will display error (or apropriate error
     * message if in production mode) and stop script execution
     *
     * @var boolean
     */
    var $is_fatal = false;

    /*
     * free custom data about error
     */
    var $additional_params = array();

    /**
     * Construct the error
     *
     * @param string $message
     * @param boolean $is_fatal
     * @return null
     */
    function __construct($message, $additional = array(), $is_fatal = null) {
      $this->setMessage($message);
      $this->setAdditionalParams($additional);

      if($is_fatal === true || $is_fatal === false) {
        $this->is_fatal = $is_fatal;
      } // if

      if($is_fatal) {
        ob_start();
        debug_print_backtrace();
        $this->setBacktrace(ob_get_clean());
      } else {
        $this->setBacktrace('Backtrace is available only for fatal errors');
      } // if

      // And log!
      $collector =& ErrorCollector::instance();
      $collector->collect($this);
    } // __construct

    /**
     * Return error params (name -> value pairs). General params are file and line
     * and any specific error have their own params...
     *
     * @param void
     * @return array
     */
    function getParams() {
      $base = array(
        'file' => $this->getFile(),
        'line' => $this->getLine()
      ); // array

      // Get additional params...
      $additional = $this->getAdditionalParams();

      // And return (join if we have additional params)
      return is_array($additional) ? array_merge($base, $additional) : $base;
    } // getParams

    /**
     * Return additional error params
     *
     * @param void
     * @return array
     */
    function getAdditionalParams() {
      return $this->additional_params;
    } // getAdditionalParams

    function setAdditionalParams($additional) {
        if (is_array($additional)) $this->additional_params = array_merge($this->additional_params, $additional);
        return $this->additional_params;
    }
    /**
     * Describe errors
     *
     * @param void
     * @return array
     */
    function describe() {
      $params = $this->getAdditionalParams();
      return is_array($params) ?
        array_merge(array('message' => $this->getMessage()), $params) :
        array('message' => $this->getMessage());
    } // describe

    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------

    /**
     * Get message
     *
     * @param null
     * @return string
     */
    function getMessage() {
      return $this->message;
    } // getMessage

    /**
     * Set message value
     *
     * @param string $value
     * @return null
     */
    function setMessage($value) {
      if (is_array($value)) {
        $this->message = implode(', ', $value);
      } else {
        $this->message = $value;
      } // if
    } // setMessage

    /**
     * Get line
     *
     * @param null
     * @return integer
     */
    function getLine() {
      return $this->line;
    } // getLine

    /**
     * Set line value
     *
     * @param integer $value
     * @return null
     */
    function setLine($value) {
      $this->line = $value;
    } // setLine

    /**
     * Get file
     *
     * @param null
     * @return string
     */
    function getFile() {
      return $this->file;
    } // getFile

    /**
     * Set file value
     *
     * @param string $value
     * @return null
     */
    function setFile($value) {
      $this->file = $value;
    } // setFile

    /**
     * Get backtrace
     *
     * @param null
     * @return array
     */
    function getBacktrace() {
      return $this->backtrace;
    } // getBacktrace

    /**
     * Set backtrace value
     *
     * @param array $value
     * @return null
     */
    function setBacktrace($value) {
      $this->backtrace = $value;
    } // setBacktrace

    function getRedirect() {
        $a = $this->getAdditionalParams();
        return $a['redirect'];
    }
    function setRedirect($url) {
        $this->setAdditionalParams(array('redirect'=>$url));
    }
  } // Error

?>
