{math assign='delta_t' equation='e-s' e=$end_time s=$start_time}
{math assign='delta_m' equation='(e-s)/1048576' e=$end_mem s=$start_mem}
<div class='bench_type_all bench_type_{$type}' depth='{$depth|default:0}'>
	<span class='indent' style='padding-left: {math equation='d*12' d=$depth}px'></span>
	{if $end_time eq 0}*{/if}
	<span {if $delta_t gt constant('BENCH_TIME_MAX')}class='max'{/if}>{$delta_t|string_format:"%.4f"} </span> | 
	<span {if $delta_m gt constant('BENCH_MEM_MAX')}class='max'{/if}>{$delta_m|string_format:"%.3f"} </span>
	{$type} {$name} {$note|escape|truncate:180:'...'}
</div>
