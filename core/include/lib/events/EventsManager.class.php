<?php

  /**
   * Events manager
   *
   */
  class EventsManager {

    /**
     * Array of event definitions
     *
     * @var array
     */
    var $events = array();

    /**
     * Determine if events queue should be sorted by priority before call
     *
     * @var boolean
     */
    var $sorted = false;


    /**
     * Counter of the current queue
     *
     * @var array
     */
    var $current_queue = array(EVENT_POST=>EVENT_POST,EVENT_PRE=>EVENT_PRE,EVENT_REPLACE=>EVENT_REPLACE);

    /**
     *  Stack of events calls in recursive hooks
     */
    var $stack = array();

    /**
     * Last returned value
     */
    var $last_return = null;
    
    /**
     * 
     */
    var $current_params = array();


    /**
     * Mode to add new listener
     * 0 - do not add repeated listener
     * 1 - add repeated listeners (use it to add same PRE and POST hook)
     * mode is reset back to 0 every time new listener is added
     */
    var $listen_mode_flag = 0;

    function setListenModeFlag($mode) {
        if ($mode) $this->listen_mode_flag = 1;
        else $this->listen_mode_flag = 0;
    }

    function sortEvents() {
        if (is_array($this->events)) {
            foreach ($this->events as $event => $funcs) {
                ksort($this->events[$event],SORT_NUMERIC);
            }
        }
        $this->sorted = true;
    }

    function getNextQueue($type) {
        $type = intval($type);
        $this->current_queue[$type] += $type;
        return $this->current_queue[$type];
    }

    /**
     * Subscribe $callback function to $event
     *
     * @param string $event
     * @param string $callback
     * @param integer $type
     * @return null
     */
    function listen($event, $callback, $type = EVENT_POST) {

        $this->sorted = false;
        $queue = $this->getNextQueue($type);

        if(!isset($this->events[$event])) {
            $this->events[$event] = array(0=>$event);
        }

        if(!in_array($callback, $this->events[$event]) || $this->listen_mode_flag == 1) {
          $this->events[$event][$queue] = $callback;
          $this->setListenModeFlag(0); // Safe reset of the flag
        } // if

    } // listen

    function unlisten($event, $callback, $type = EVENT_POST) {
        if (!is_array($this->events[$event])) return false;

        foreach ($this->events[$event] as $queue => $cb) {
            if ($cb==$callback && $type*$queue>=0) {
                // if callback found in queue
                unset($this->events[$event]);
                if ($type == EVENT_REPLACE) {
                    // if replacement deleted - restore original function
                    $this->events[$event] = array(0=>$event);
                }
                break;
            }
        }
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
     *
     * @param string $event
     * @param array $params
     * @param mixed $result
     * @return mixed
     */
    function trigger($event, $params, $result = null) {
		
	  $trigger_bench_id = cw_bench_open_tag($event, 'call', '');
	  
      $this->last_return = null;
      $callback_result = null;
      
      // if not exist - init event "on the fly"
      if (!isset($this->events[$event]) || !is_array($this->events[$event])) {
          $this->events[$event] = array(0=>$event);
      }

      if (!$this->sorted) $this->sortEvents();
      
      foreach($this->events[$event] as $callback) {

        if (isset($this->events[$callback]) && $callback!=$event) {
            // Hook also has own hooks
            $callback_return = $this->trigger($callback,$params,$result);
        }
        elseif (function_exists($callback)) {
            $callback_return = call_user_func_array($callback, $params);
        }
        elseif (strpos($event,'on_')!==0) {
            error_log("Expected function '$event' was not found. Check if it is defined and spelled correctly");
        }
        else {
            continue;
        }

        // Special handler of returned class EventReturn, which allows to return value and change inpput params
        if (instance_of($callback_return,'EventReturn')) {
            if (!is_null($callback_return->params))
                $params = $callback_return->params;
            $callback_return = $callback_return->getReturn();
        }

        // null returned by hook means to ignore this hook in chain of results
        if (!is_null($callback_return)) {
            
            $callback_result = $callback_return;

            $this->last_return = $callback_result;

            if(is_array($result) && !is_null($callback_result)) {
              $result[$callback] = $callback_result;
            } elseif(is_string($result)) {
              $result .= $callback_result;
            } elseif(is_int($result) || is_float($result)) {
              $result += $callback_result;
            }
        }

      } // foreach


      $this->last_return = null;
      
      cw_bench_close_tag($trigger_bench_id);
      return is_null($result)?$callback_result:$result;
    } // trigger


    /**
     * Return manager instance
     *
     * @param void
     * @return EventsManager
     */
    static function &instance() {
      static $instance = null;
      if($instance === null) {
        $instance = new EventsManager();
      } // if
      return $instance;
    } // instance

  } // EventsManager

?>
