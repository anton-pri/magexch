<?php
include $app_main_dir.'/include/lib/smarty/Smarty.class.php';
include $app_main_dir.'/include/lib/smarty/Smarty_Compiler.class.php';

if (!class_exists('Smarty')) {
	echo "Can't find template engine!";
	exit;
}

class Templater extends Smarty {

	function __construct() {
		global $app_main_dir;

		$this->strict_resources = array ();

		$this->request_use_auto_globals = true;
		array_unshift($this->plugins_dir, $app_main_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'templater'.DIRECTORY_SEPARATOR.'plugins');

        $this->force_compile = false;

		$this->compiler_file	= "templater.php";
		$this->compiler_class	= "TemplateCompiler";

		$this->compile_check_md5 = false;

        $this->security = true;
        $this->security_settings = array(
            'PHP_HANDLING'    => false,
            'IF_FUNCS'        => array('array', 'list',
                'isset', 'empty',
                'count', 'sizeof',
                'in_array', 'is_array', 'array_keys',
                'true', 'false', 'null',
                'constant'),
            'INCLUDE_ANY'     => false,
            'PHP_TAGS'        => false,
            'MODIFIER_FUNCS'  => array('count', 'capitalize', 'cat', 'count_characters', 'count_paragraphs', 'count_sentences', 'count_words', 'date_format', 'default', 'escape', 'indent', 'lower', 'nl2br', 'regex_replace', 'replace', 'spacify', 'string_format', 'strip', 'strip_tags', 'truncate', 'upper', 'wordwrap', 'abs_value', 'amp', 'comma_format', 'formatnumeric', 'formatprice', 'id', 'substitute', 'substitute', 'stripslashes', 'date', 'round', 'strval', 'md5', 'strtolower','strlen','base64_decode','json_decode','json_encode'),
            'ALLOW_CONSTANTS'  => true,
            'ALLOW_SUPER_GLOBALS' => true,
        );

		return parent::__construct();
	}

    function parse_ini_str($content, $name = '') {
        if(!is_object($this->_parse_ini_obj)) {
            require_once SMARTY_DIR . $this->config_class . '.class.php';
            $this->_parse_ini_obj = new $this->config_class();
            $this->_parse_ini_obj->overwrite = $this->config_overwrite;
            $this->_parse_ini_obj->booleanize = $this->config_booleanize;
            $this->_parse_ini_obj->read_hidden = $this->config_read_hidden;
            $this->_parse_ini_obj->fix_newlines = $this->config_fix_newlines;
        }
# kornev
# only required data
        $ret = array();
        $buttons = array();
        $_tmp = $this->_parse_ini_obj->parse_contents($content);
        $js_tab = $_tmp['vars']['default_tab'];
        $default_template = $_tmp['vars']['default_template'];
        if (is_array($_tmp['sections']))
            foreach($_tmp['sections'] as $section=>$vars) {
                if ($section == 'submit' || $section == 'reset' || $vars['vars']['type']=='button') {
                    $buttons[] = $vars['vars'];
                    continue;
                }
                if (!$vars['vars']['template']) $vars['vars']['template'] = $default_template;
                $ret[$section] = $vars['vars'];
                if (!$js_tab || !$_tmp['sections'][$js_tab]) $js_tab = $section;
            }
        $ret = cw_func_call('cw_tabs_js_abstract', array('name' => $name, 'selected' => $js_tab, 'js_tabs' => $ret, 'buttons' => $buttons));
# kornev, we should defaine the selected tab....
# kornev, try to find the default once again
        if ($ret['selected'] != $_tmp['vars']['default_tab']) {
            if (isset($ret['js_tabs'][$_tmp['vars']['default_tab']])) $ret['selected'] = $_tmp['vars']['default_tab'];
        }
        return $ret;
    }

	function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false) {
		$this->current_resource_name = $resource_name;
		return parent::fetch($resource_name, $cache_id, $compile_id, $display);
	}

	function _is_compiled($resource_name, $compile_path) {
		if (!empty($this->strict_resources)) {
			foreach ($this->strict_resources as $rule)
				if (preg_match($rule, $resource_name)) return false;
		}

		$result = parent::_is_compiled($resource_name, $compile_path);
		if ($result && $this->compile_check_md5)
			return $this->_check_compiled_md5($compile_path);

		return $result;
	}

	#
	# Test if compiled resource was changed by third party
	#
	function _check_compiled_md5($compiled_file) {

		if ((rand() % 10) != 5) return true;

		$control_file = $compiled_file.'.md5';

		$compiled_data = $this->_read_file($compiled_file);
		if ($compiled_data === false)
			return false;

		$control_data = $this->_read_file($control_file);
		if ($control_data === false)
			return false;

		$md5 = md5($compiled_file.$compiled_data);
		return !strcmp($md5,$control_data);
	}

	function _compile_resource($resource_name, $compile_path, $is_literal = 0) {
		$result = parent::_compile_resource($resource_name, $compile_path, $is_literal);

		if ($result && $this->compile_check_md5) {
			$tpl_source = $this->_read_file($compile_path);
			if ($tpl_source !== false) {
				$_params = array(
					'filename' => $compile_path.'.md5',
					'contents' => md5($compile_path.$tpl_source),
					'create_dirs' => true
				);
				smarty_core_write_file($_params, $this);
			}
		}

		return $result;
	}

	// Implementation of {include} tag with hooks
    function _smarty_include($params)
    {
        global $ars_hooks;
        $pre = $post = $replace = array();
        $orig_file = $params['smarty_include_tpl_file'];

        $bench_id = cw_bench_open_tag($orig_file,'tpl');

        if (is_array($ars_hooks['tpl'][$orig_file]) && !$params['smarty_include_vars']['disable_hooks'])
        foreach($ars_hooks['tpl'][$orig_file] as $type=>$files) {
            foreach($files as $file) {
                if ($orig_file == $file[0]) continue; // skip loop in hooks
                $file[2] = $type;
                $file[0] = $file[0];
                if (
                      !isset($file[1]) 
                   || $this->_tpl_vars[$file[1]] 
                   || (function_exists($file[1]) && cw_call($file[1],array(&$params['smarty_include_vars'])))
                   || (strpos($file[1],'.tpl')!==false && $this->_tpl_vars['_current_compiled_file']==$file[1])
                ) {
                    switch($type) {
                        case 'replace':
                            $replace[] = $file;
                            break;
                        case 'pre':
                            $pre[] = $file;
                            break;
                        case 'post':
                            $post[] = $file;
                            break;
                    }
                }
        }
        }

        if (empty($replace)) $replace[] = array($orig_file, '', 'original');
        $include_files = array_merge($pre, $replace, $post);
        foreach($include_files as $file) {
            if (in_array($file[2], array('pre', 'post','replace'))) {
                $params['smarty_include_tpl_file'] = $file[0];
                $this->_smarty_include($params);
            }
            elseif ($file[2] == 'original') {
                $params['smarty_include_tpl_file'] = $file[0];
                if (!$this->template_exists($params['smarty_include_tpl_file'])) continue; // skip non-existent tpl if it has hooks 
                parent::_smarty_include($params);
            }
        }

       cw_bench_close_tag($bench_id);

    }	
	
};

class TemplateCompiler extends Smarty_Compiler {
	function _compile_file($resource_name, $source_content, &$compiled_content) {
		$this->current_resource_name = $resource_name;

		return parent::_compile_file($resource_name, $source_content, $compiled_content);
	}

	// Implementation of {include_once} tag
    function _compile_include_once_tag($tag_args, $field = 'file') {
        $attrs = $this->_parse_attrs($tag_args);

        if (empty($attrs[$field])) {
            $this->_syntax_error("missing '$field' attribute in include_once tag", E_USER_ERROR, __FILE__, __LINE__);
        }

        $md5 = md5($attrs[$field]);
 
        $output = "if (!\$this->_included_files['$md5']): \n";
        $output .= "\$this->_included_files['$md5'] = true;\n";
		$output .= '?>';

        $output .= $this->_compile_include_tag($tag_args);

        $output .= '<?php endif;';

        return $output;
    }    
};

?>
