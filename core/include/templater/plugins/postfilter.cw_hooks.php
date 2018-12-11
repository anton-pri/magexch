<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     postfilter.cw_hooks.php
 * Type:     postfilter
 * Name:     cw_hooks
 * Purpose:  Replace special markup to hooks calls
 * -------------------------------------------------------------
 */
function smarty_postfilter_cw_hooks($compiled, &$compiler) {
   global $_current_compiled_file;
   $_current_compiled_file = $compiler->_current_file;
   $compiled = preg_replace_callback('|<!--\s*cw@([\w_\d:#]+)\s*[\[\]]?\s*-->|is','smarty_postfilter_cw_hooks_callback', $compiled);
   $precode = <<< EOT
<?php global \$ars_hooks;
   \$this->_tpl_vars['_current_compiled_file'] = "$_current_compiled_file";
?>
EOT;
   return $precode.$compiled;
}

function smarty_postfilter_cw_hooks_callback($matches) {
    global $_current_compiled_file;
    
    $case = 0;  // Single point <!-- cw@id -->
    if (strpos($matches[0],'[')!==false) $case = 1; // Open block tag <!-- cw@id [ -->
    if (strpos($matches[0],']')!==false) $case = 2; // Close block tag <!-- cw@id ] -->

if ($case == 0) {
$code = <<<EOT
$matches[0]
<?php
    \$_hook_name = '$_current_compiled_file@$matches[1]';
    if (\$ars_hooks['tpl'][\$_hook_name]) {
        \$this->_smarty_include(array('smarty_include_tpl_file' => \$_hook_name, 'smarty_include_vars' => array()));
    }
?>
EOT;
}
if ($case == 1) {
$code = <<<EOT
$matches[0]
<?php
    \$_hook_name = '$_current_compiled_file@$matches[1]';
    if (\$ars_hooks['tpl'][\$_hook_name]) {
        cw_addons_split_hook(\$_hook_name);
    }
    if (\$ars_hooks['tpl'][\$_hook_name.'#pre']) {
        \$this->_smarty_include(array('smarty_include_tpl_file' => \$_hook_name.'#pre', 'smarty_include_vars' => array()));
    }
    if (\$ars_hooks['tpl'][\$_hook_name.'#replace']) {
        \$this->_smarty_include(array('smarty_include_tpl_file' => \$_hook_name.'#replace', 'smarty_include_vars' => array()));
    }
    if (empty(\$ars_hooks['tpl'][\$_hook_name.'#replace'])):
?>
EOT;
}
if ($case == 2) {
$code = <<<EOT
<?php
    endif;
    
    \$_hook_name = '$_current_compiled_file@$matches[1]';
    
    if (\$ars_hooks['tpl'][\$_hook_name.'#post']) {
        \$this->_smarty_include(array('smarty_include_tpl_file' => \$_hook_name.'#post', 'smarty_include_vars' => array()));
    }
?>
$matches[0]
EOT;
}

    return $code;
    
}
