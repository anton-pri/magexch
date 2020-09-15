{*include file='common/page_title.tpl' title=$lng.lbl_shop_logs*}


<script type="text/javascript" language="JavaScript 1.2"><!--

function managedate(type, status) {ldelim}

	var fields = new Array('StartDay','StartMonth','StartYear','EndDay','EndMonth','EndYear');

	for (var i in fields)
		document.searchform.elements[fields[i]].disabled = status;

{rdelim}

--></script>

{capture name=block}

<p>{$lng.txt_shop_logs_top_text}</p>
<form name="searchform" action="index.php?target=logs" method="post" class="form-horizontal">
<input type="hidden" name="action" value="" />
<div class="form-group">
<label class="col-xs-12">{$lng.lbl_date_period}:</label>
<div class="col-xs-12" >
	<div class="radio">
		<label for="date_period_all"><input id="date_period_all" type="radio" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript: managedate('date',true)" />
		{$lng.lbl_all_dates}&nbsp;&nbsp;</label>
	</div>
	<div class="radio">
		<label for="date_period_M"><input id="date_period_M" type="radio" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" />
		{$lng.lbl_this_month}&nbsp;&nbsp;</label>
	</div>
	<div class="radio">
		<label for="date_period_W"><input id="date_period_W" type="radio" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" />
		{$lng.lbl_this_week}&nbsp;&nbsp;</label>
	</div>
	<div class="radio">
		<label for="date_period_D"><input id="date_period_D" type="radio" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" />
		{$lng.lbl_today}</label>
	</div>
	<div class="radio">
		<label for="date_period_C"><input id="date_period_C" type="radio" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" />
		{$lng.lbl_specify_period_below}</label>
	</div>
</div>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_log_date_from}:</label>
<div class="col-xs-12">{html_select_date prefix="Start" time=$search_prefilled.start_date start_year=$config.Company.start_year end_year=$config.Company.end_year all_extra="class=\"form-control inline-control\""}</div>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_log_date_through}:</label>
<div class="col-xs-12">{html_select_date prefix="End" time=$search_prefilled.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year display_days=yes all_extra="class=\"form-control inline-control\""}</div>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_log_include_logs}:</label>
<div class="col-xs-12">
{foreach key=log_label item=txt_label from=$log_labels}
	<div class="checkbox">
    	<label for="ll_{$log_label}" style="width:auto;">
    		<input id="ll_{$log_label}" type="checkbox" name="posted_data[logs][]" value="{$log_label}"{if $search_prefilled.logs.$log_label ne ""} checked="checked"{/if} />&nbsp; 
    		{$txt_label|replace:'\':'_'} {$log_label}
    	</label>
    </div>
{/foreach}
</div>
</div>

<div class="form-group">
<label class="col-xs-12">{$lng.lbl_log_records_count}:</label>
<div class="col-xs-12">
	<input type="text" class="form-control form-control-inline" name="posted_data[count]" value="{$search_prefilled.count}" size="5" />
	<font class="SmallText">{$lng.lbl_log_records_count_note}</font>
</div>
</div>

<div class="buttons">
	<input type="submit" class="btn btn-green push-20 push-5-r" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: cw_submit_form('searchform');" />
	<input type="submit" class="btn btn-danger push-20 push-5-r" value="{$lng.lbl_log_clean_selected|strip_tags:false|escape}" onclick="javascript: cw_submit_form('searchform', 'clean');" />
</div>

{if $search_prefilled.date_period ne "C"}
<script type="text/javascript" language="JavaScript 1.2"><!--
managedate('date',true);
--></script>
{/if}

</form>


{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}


{if $show_results ne ""}
{capture name=block_l}
{if $logs ne ""}
{foreach key=label item=data from=$logs}
<a name="result_{$label}"></a>
{include file="common/subheader.tpl" title=$log_labels.$label|default:$label}
<div class="input_field_1">
{$data|replace:"-------------------------------------------------\n":'<hr size="1" noshade="noshade" />'|replace:"\n":"<br />"|replace:"``":"&ldquo;"|replace:"''":"&rdquo;"}
</div>
{/foreach}
{else}{* $logs ne "" *}
{$lng.lbl_log_no_entries_found}
{/if}{* $logs ne "" *}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block_l title=$lng.lbl_search_results}
{/if}{* $show_results ne "" *}
