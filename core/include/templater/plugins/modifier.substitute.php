<?php
function smarty_modifier_substitute() {
    $args = func_get_args();
    if (empty($args))
        return "";

    $string = $args[0];
    $length = count($args);
    $replace_to = array();

    for ($i = 1; $i < $length; $i+=2) {
        $replace_to[$args[$i]] = $args[$i+1];
    }

    if (is_array($replace_to)) {
        foreach ($replace_to as $k=>$v) {
            $string = str_replace("{{".$k."}}", $v, str_replace("~~".$k."~~", $v,$string));
        }
    }

    return $string;
}

?>
