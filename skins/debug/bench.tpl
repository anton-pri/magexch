{capture assign=debug_output}
<html>
<head>
<title>Benchmark</title>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 {literal}
<style>
body, h1, h2, td, th, p {
    font-family: sans-serif;
    font-weight: normal;
    font-size: 0.8em;
    margin: 1px;
    padding: 0;
}
span.max {
	color: red;
}	
.bench_type_sql {
	border-left: 3px solid grey;
}
.bench_type_include {
	border-left: 3px solid green;
}
.bench_type_call {
	border-left: 3px solid blue;
}
.bench_type_tpl {
	border-left: 3px solid yellow;
}
.bench_toolbar span {
	margin-left: 10px;
	display: inline-block;
	border: 1px solid black;
	padding: 3px;
	cursor: pointer;
	
}
.bench_type_all span.indent {
	background: url("{/literal}{$ImagesDir}{literal}/arrow-menu-entry.png") repeat-x scroll 0 -3px rgba(0, 0, 0, 0);
}
</style>
{/literal}
</head>
<body>
{math assign='max_mem' equation='e/1048576' e=$bench_max_memory}
<p>MAX USED MEMORY: {$max_mem|string_format:"%.3f"} Mb</p>
<div class='bench_toolbar'><span>all</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>sql</span><span>include</span><span>call</span></div>
time | mem  &gt; block_type block_name<br />
* time between blocks is shown only when execution exceeds max memory or time boundaries (see bench_exec* log for full execution plan)
<hr>
{foreach from=$bench item=v}

{if $prev_v and $prev_v.depth lt $v.depth}
	{math assign='delta_t' equation='e-s' e=$v.start_time s=$prev_v.start_time}
	{math assign='delta_m' equation='(e-s)/1048576' e=$v.start_mem s=$prev_v.start_mem}
	{if $delta_t gt $smarty.const.BENCH_TIME_MAX || $delta_m gt $smarty.const.BENCH_MEM_MAX}
	{include file='debug/bench_row.tpl' name='PHP' type='php' note='' 
	start_time=$prev_v.start_time end_time=$v.start_time start_mem=$prev_v.start_mem end_mem=$v.start_mem depth=$v.depth}
	{/if}
{else}
	{math assign='delta_t' equation='e-s' e=$v.start_time s=$prev_v.end_time}
	{math assign='delta_m' equation='(e-s)/1048576' e=$v.start_mem s=$prev_v.end_mem}
	{if $delta_t gt $smarty.const.BENCH_TIME_MAX || $delta_m gt $smarty.const.BENCH_MEM_MAX}
	{include file='debug/bench_row.tpl' name='PHP' type='php' note='' 
	start_time=$prev_v.end_time end_time=$v.start_time start_mem=$prev_v.end_mem end_mem=$v.start_mem depth=$v.depth}
	{/if}
{/if}
{include file='debug/bench_row.tpl' name=$v.name type=$v.type note=$v.note 
	start_time=$v.start_time end_time=$v.end_time start_mem=$v.start_mem end_mem=$v.end_mem depth=$v.depth}

{assign var=prev_v value=$v}
{/foreach}
{literal}
<script>
$('.bench_toolbar span').click(function(){

	what = $(this).text();
	$('.bench_type_all').hide();
	if (!isNaN(parseFloat(what))) { 
		for (i=0; i<=what; i++)
			$('.bench_type_all[depth='+i+']').show();
	}
	else $('.bench_type_'+what).show();
	
	if ( window.console && window.console.log ) console.log('Show '+what+' '+$('.bench_type_all:visible').length);
	
});
</script>
{/literal}
</body>
</html>
{/capture}

{if isset($_smarty_debug_output) and $_smarty_debug_output eq "html"}
    {$debug_output}
{else}
<script type="text/javascript">
// <![CDATA[
    if ( self.name == '' ) {ldelim}
       var title = 'bench';
    {rdelim}
    else {ldelim}
       var title = 'bench_' + self.name;
    {rdelim}

    _smarty_console = window.open("",title,"resizable,scrollbars=yes");
    _smarty_console.document.write('{$debug_output|escape:'javascript'}');
    _smarty_console.document.close();
// ]]>
</script>
{/if}
