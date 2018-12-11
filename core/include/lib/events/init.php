<?php

  /**
   *  Constants to define hooks place in the queue, see cw_set_hook(), cw_event_listen(), cw_set_controller()
   */
  define('EVENT_POST',1);
  define('EVENT_REPLACE',0);
  define('EVENT_PRE',-1);

  /**
   *  Constants to define scope of the includes
   */
   define('INCLUDE_NO_GLOBALS',0); // include with local scope without global vars, all globals must be declared explicitly (recommended for development)
   define('INCLUDE_R_GLOBALS',1);  // include with extract($GLOBALS) - all global values are available in local scope
   define('INCLUDE_RW_GLOBALS',2); // include with declared all global vars (default)

  global $app_main_dir, $eventsManager, $includesManager;

  require_once $app_main_dir.'/include/lib/events/EventsManager.class.php';
  require_once $app_main_dir.'/include/lib/events/EventReturn.class.php';
  require_once $app_main_dir.'/include/lib/events/IncludesManager.class.php';


  $eventsManager   =& EventsManager::instance();
  $includesManager =& IncludesManager::instance();


/** EVENTS MECHANISM **/

  /**
   * Subscribe $callback to an $event
   *
   * $events can be an array of events or single even name
   *
   * @param array $events
   * @param string $callback
   * @param integer $type
   * @return null
   */
  function cw_event_listen($event, $callback, $type = EVENT_POST) {
    global $eventsManager;

    $eventsManager->listen($event, $callback, $type);

  } // event_listen

  function cw_event_delete_listener($event, $callback, $type = EVENT_POST) {
    global $eventsManager;

    $eventsManager->unlisten($event, $callback, $type);
  }
  /**
   * Trigger specific event with a given parameters
   *
   * $result is start value of result. It determines how data returned from
   * callback functions will be handled. If $result is:
   *
   * - array - values will be added as new elements
   * - integer or float - values will be added to the $result
   * - string - values will be appended to current value
   * - null - last value returned from callback function
   *
   * @param string $event
   * @param array $params
   * @param mixed $result
   * @return mixed
   */
  function cw_event($event, $params = array(), $result = null) {
    global $eventsManager;

    return $eventsManager->trigger($event, $params, $result);
  } // event_trigger


    /**
     *  Function returns all associated hooks/handlers/includes
     *
     *  @param null|''|string $func
     *
     *  use function name, event or included file to get all associated hooks
     *  use null to get all hooks/handlers
     *  use '' to get all overrided includes
     */
    function cw_get_hooks($func = null) {

        if (is_null($func) || (strpos($func,'.') === false && !empty($func))) {
            global $eventsManager;
            $instance =& $eventsManager;
        } else {
            global $includesManager;
            $instance =& $includesManager;
        }

        if (empty($func)) return $instance->events;
        else return $instance->events[$func];
    }

    /**
     *  Function return result of previous handler call
     *  can be used to get access to previous result to make chain processing
     */

     function cw_get_return() {
         global $eventsManager;
         return $eventsManager->last_return;
     }

/** HOOKS MECHANISM **/
/* based on events */

  function cw_set_hook($hook, $callback, $type = EVENT_POST) {
        cw_event_listen($hook, $callback, $type);
  }
  function cw_delete_hook($hook, $callback, $type = EVENT_POST) {
        cw_event_delete_listener($hook, $callback, $type);
  }
    // Alias for cw_delete_hook()
  function cw_unset_hook($hook, $callback, $type = EVENT_POST) {
        cw_event_delete_listener($hook, $callback, $type);
  }
  // Alias for cw_event()
  function cw_call($func, $params = array()) {
    global $eventsManager;
    return $eventsManager->trigger($func, $params, null);
  }

/** INCLUDES MECHANISM **/
/* inherited from events */
  function cw_set_controller($file, $callback, $type = EVENT_POST) {
    global $app_main_dir;
    global $includesManager;

    $includesManager->listen($app_main_dir.with_leading_slash($file), $app_main_dir.with_leading_slash($callback), $type);
  }

  function cw_unset_controller($file, $callback, $type = EVENT_POST) {
    global $app_main_dir;
    global $includesManager;

    $includesManager->unlisten($app_main_dir.with_leading_slash($file), $app_main_dir.with_leading_slash($callback), $type);
  }

  function cw_include($file, $mode = INCLUDE_RW_GLOBALS) {

    global $app_main_dir;
    global $includesManager;

    return $includesManager->trigger($app_main_dir.with_leading_slash($file), $mode);
  }

/**
 * Include file with functions
 */
function cw_include_once($file) {
    static $included;

    if (!$included) $included = array();

    if (isset($included[$file])) {
        return false;
    }

    $included[$file] = 1;
    return cw_include($file);
    
}
