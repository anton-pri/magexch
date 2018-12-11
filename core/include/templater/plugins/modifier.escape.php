<?php
function smarty_modifier_escape($string, $esc_type = 'html', $char_set = 'ISO-8859-1') {
    if (zerolen($string))
        return $string;

    switch ($esc_type) {
        case 'html':
            if (phpversion() >= '4.1.0')
                return htmlspecialchars($string, ENT_QUOTES, $char_set);
            else
                return htmlspecialchars($string, ENT_QUOTES);

        case 'htmlall':
            if (phpversion() >= '4.1.0')
                return htmlentities($string, ENT_QUOTES, $char_set);
            else
                return htmlentities($string, ENT_QUOTES);

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F', '/', rawurlencode($string));
            
        case 'quotes':
            return preg_replace("/(?<!\\\\)'/Ss", "\\'", $string);

        case 'hex':
            $s = '%';
        case 'hexentity':
            if (!$s)
                $s = '&#x';
        case 'decentity':
            if (!$s)
                $s = '&#';
            $f = ($esc_type == 'decentity') ? "ord" : "bin2hex";
            $l = strlen($string);
            $return = '';
            for ($x = 0; $x < $l; $x++)
                $return .= $s.$f(substr($string, $x, 1)).';';

            return $return;

        case 'javascript':
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
           
        case 'json':
// kornev, we don't need "'"=>"\\'", for json - it will work fine when we are quote it this way "test ' test"
            return strtr($string, array('\\'=>'\\\\','"'=>'\\"',"\r"=>'',"\n"=>'','</'=>'<\/',"\t"=>'    '));
        
        case 'clear':
             return strtr($string, array("'"=>"",'"'=>'',"\r"=>'',"\n"=>''));

        case 'mail':
            return strtr($string, array('@', '.'), array(' [AT] ', ' [DOT] '));
            
        case 'nonstd':
            $return = '';
            $l = strlen($string);
            for ($i = 0; $i < $l; $i++) {
                $symbol = substr($string, $i, 1);
                $ord = ord($symbol);
                $return .= ($ord >= 126) ? ('&#'.$ord.';') : $symbol;
            }
            return $return;

        case 'tooltip':
            return strip_tags($string);
    }

    return $string;
}
?>
