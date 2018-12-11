<?php

function cw_var_dump() {
    static $count = 0;
    global $customer_id;

    $args = func_get_args();

    $msg = "<div align=\"left\"><pre><font>";
    $log = "Logged as: $customer_id\n";
    if (!empty($args)) {
        foreach ($args as $index=>$variable_content){
            $msg .= "<b>Debug [".$index.'/'.$count."]:</b> ";
            $log .= "Debug [".$index.'/'.$count."]: ";
            ob_start();
            var_dump($variable_content);
            $data = ob_get_contents(); ob_end_clean();
            $msg .= htmlspecialchars($data)."\n";
            $log .= $data."\n";
        }
    }
    else {
        $msg .= '<b>Debug notice:</b> try to use cw_var_dump($varname1,$varname2); '."\n";
        $log .= 'Debug notice: try to use cw_print_r($varname1,$varname2); '."\n";
    }

    $msg .= "</font></pre></div>";

    if (defined('IS_AJAX')) cw_ajax_add_block(array(
        'id' => 'cw_var_dump',
        'content' => $log,
        'action' => 'console') );
    else echo $msg;

    $count++;

}

/**
 * This function displays how much memory currently is used
 */
function cw_get_memory_used($label="")
{
    $backtrace = debug_backtrace();
    echo $label . " File: " . $backtrace[0]['file'] . "<br />Line: " . $backtrace[0]['line'] . "<br />Memory is used: " . memory_get_usage() . "<hr />";
}

function cw_timer($label)
{
    static $timer;
    $now = cw_core_microtime();
    if (empty($timer)) $timer=$now;
    if (!defined('IS_AJAX')) echo $label.': '.($now-$timer)."<br/>\n";
    $timer=$now;
}
