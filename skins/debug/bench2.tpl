<pre>
{math assign='max_mem' equation='e/1048576' e=$bench_max_memory}
MAX USED MEMORY: {$max_mem|string_format:"%.3f"} Mb

[TIME_POINT][TIME_DIFF] point_type point_name {ldelim} note {rdelim} // block_duration sec
* search for "!*" to see long time execution more than max limit
{foreach from=$bench_timelog item='i' key='time'}
{if $i lt 0}{math assign=k equation='abs(u)' u=$i}{else}{assign var=k value=$i}{/if}
{assign var=ind value=$bench[$k].depth}
{math assign='delta_t' equation='e-s' e=$time s=$prev_time}
{assign var=prev_time value=$time}
[{$time|string_format:"%.4f"}][{if $delta_t gt constant('BENCH_TIME_MAX')}!*{/if}{$delta_t|string_format:"%.4f"}]{' '|indent:$ind:' .'}{if $i>0}{$bench[$i].type} {$bench.$i.name} {ldelim} {$bench.$i.note}{else}{math assign='delta_t' equation='e-s' e=$bench[$k].end_time s=$bench[$k].start_time}{rdelim} // {$bench[$k].type} {if $delta_t gt constant('BENCH_TIME_MAX')}!*{/if}{$delta_t|string_format:"%.4f"} sec{/if}
{/foreach}

</pre>
