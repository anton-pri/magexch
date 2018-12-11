{assign_debug_info}

<script type="text/javascript" language="JavaScript 1.2">
<!--
	if (window.opener==null || "{$opener}"!="console") {ldelim}
	_smarty_console = window.open("","console","width=400,height=500,resizable,scrollbars=yes");
	if(_smarty_console) {ldelim}
	_smarty_console.document.open();
	_smarty_console.document.write('<html><title>Smarty Debug Console</title><link rel="stylesheet" href="{$SkinDir}/general.css" media="screen" /><body>');
	_smarty_console.document.write('<b>{$lng.lbl_included_templates_config_files|strip_tags}:</b><br/>');
	{section name=templates loop=$_debug_tpls}
		_smarty_console.document.write('{section name=indent loop=$_debug_tpls[templates].depth}&nbsp;&nbsp;&nbsp;{/section}<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" />&nbsp;');
        {if false and $_debug_tpls[templates].type eq "template"}
        _smarty_console.document.write('<span onmouseover="javascript: if (mainWnd && mainWnd.tmo) mainWnd.tmo(\'{$_debug_tpls[templates].filename|replace:"/":"0"}\')" onmouseout="javascript: if (mainWnd && mainWnd.tmu) mainWnd.tmu(\'{$_debug_tpls[templates].filename|replace:"/":"0"}\')">{$_debug_tpls[templates].filename}</span>');
        {else}
		_smarty_console.document.write('{$_debug_tpls[templates].filename}&nbsp;')
        {/if}
		_smarty_console.document.write('({$_debug_tpls[templates].exec_time|string_format:"%.5f"}){if %templates.index% eq 0} (total){/if}');
		_smarty_console.document.write('<br/>');
	{sectionelse}
		_smarty_console.document.write('<i>no templates included</i>');	
	{/section}
	_smarty_console.document.write('</body></html>');
	_smarty_console.document.close();
	_smarty_console.mainWnd = window;
	{rdelim}
	{rdelim}
-->
</script>
