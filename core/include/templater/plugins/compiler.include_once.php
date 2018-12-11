<?php
function smarty_compiler_include_once($tag_args, &$compiler) {
	if (method_exists($compiler, '_compile_include_once_tag'))
		return $compiler->_compile_include_once_tag($tag_args, 'file');
	else {
		$compiler->_syntax_error("tag 'include_once' is not implemented in compiler", E_USER_WARNING);
        return;
	}
	
}
