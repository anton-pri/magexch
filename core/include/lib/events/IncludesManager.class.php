<?php

  /**
   * Includes manager
   * 
   * same as EventsManager but for files includes instead of functions calls
   */
  class IncludesManager extends EventsManager {

    function listen($event, $callback, $type = EVENT_POST) {

        $this->sorted = false;
        $queue = $this->getNextQueue($type);

        if(!isset($this->events[$event])) {
            $this->events[$event] = array();
            if (file_exists($event)) $this->events[$event][0] = $event;
        }

        if(!in_array($callback, $this->events[$event]) || $this->listen_mode_flag == 1) {
          $this->events[$event][$queue] = $callback;
          $this->setListenModeFlag(0); // Safe reset of the flag
        } // if

    } // listen

    /**
     * Trigger specific event with a given parameters
     */
    function trigger($_event_, $include_mode = INCLUDE_RW_GLOBALS, $result=null) {

    $trigger_bench_id = cw_bench_open_tag($_event_, 'include', '');

    // Restore global scope in read only or read/write mode
      if ($include_mode == INCLUDE_RW_GLOBALS) {
         foreach ($GLOBALS as $gkey => $gval)
            global $$gkey;
      }
      elseif ($include_mode == INCLUDE_R_GLOBALS) {
    /* TODO: useless mode, remove it */
          extract($GLOBALS);
      }

      $event = $_event_; // restore $event var in case it was in global scope

      if(isset($this->events[$event])) {
        if(is_array($this->events[$event])) {
          if (!$this->sorted) $this->sortEvents();
          foreach($this->events[$event] as $callback) {

            // Go baby go...
            if (isset($this->events[$callback]) && $callback!=$event) {
                $this->trigger($callback, $include_mode);
            }
            else {
			    $callback_id = cw_bench_open_tag($callback, 'include', '');
                  if (file_exists($callback)) 
                    $callback_result = include $callback;
                  else
                    $callback_result = null;                
				cw_bench_close_tag($callback_id);
            }

          } // foreach
        } // if
      } // if
      else {

          if (file_exists($event)) $callback_result = include $event;
          else  $callback_result = null;
      }

      cw_bench_close_tag($trigger_bench_id);
      
      return $callback_result;
      
    } // trigger

    /**
     * Return manager instance
     *
     * @param void
     * @return IncludesManager
     */
    static function &instance() {
      static $instance = null;
      if($instance === null) {
        $instance = new IncludesManager();
      } // if
      return $instance;
    } // instance

  } // IncludesManager

?>
