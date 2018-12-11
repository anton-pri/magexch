{capture name=section}
{capture name=block}

<form name="filter_logs_form" method="post" action="index.php?target=logging" class="form-horizontal">
<input type="hidden" name="action" value="filter_logs" />
<div class="box">
    <div class="subheader">
        <span class="subheader_left">Filter log</span>
    </div>
    <div class="form-group form-inline">

    	<label class="col-xs-12">Date:</label>
    	<div class="col-xs-12">
    		<div class="form-group">{include file="main/select/date.tpl" name="logs_filter[date][date_start]" value=$logging_filter.date.date_start}</div>
    		<div class="form-group"> - </div>
    		<div class="form-group">{include file="main/select/date.tpl" name="logs_filter[date][date_end]" value=$logging_filter.date.date_end}</div>
		</div>
    </div>

    <div class="form-group">

        <label class="col-xs-12">Visitor is logged in:</label>
        <div class="col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[is_logged][yes]" {if $logging_filter.is_logged.yes eq 1}checked="checked"{/if}/>Yes&nbsp;
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[is_logged][no]" {if $logging_filter.is_logged.no eq 1}checked="checked"{/if} />No&nbsp;
                        </label>
                    </div>
        </div>
 
    </div>
    <div class="form-group">
         <label class="col-xs-12">Current area:</label>
         <div class="col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[current_area][C]" {if $logging_filter.current_area.C eq 1}checked="checked"{/if}/>Customer&nbsp;
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[current_area][A]" {if $logging_filter.current_area.A eq 1}checked="checked"{/if} />Admin&nbsp;
                        </label>
                    </div>
        </div>

    </div>

    <div class="form-group">
                <label class="col-xs-12">REQUEST URI:</label>
				<div class="col-xs-12">
                    <input type="text" class="form-control" value="{$logging_filter.REQUEST_URI}" name="logs_filter[REQUEST_URI]" />
				</div>
    </div>
    <div class="form-group">
                <label class="col-xs-12">Method:</label>
                <div class="col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[REQUEST_METHOD][GET]" {if $logging_filter.REQUEST_METHOD.GET eq 1}checked="checked"{/if}/>GET
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[REQUEST_METHOD][POST]" {if $logging_filter.REQUEST_METHOD.POST eq 1}checked="checked"{/if} />POST
                        </label>
                    </div>
				</div>
    </div>
    <div class="form-group">
                <label class="col-xs-12">target/code:</label>
				<div class="col-xs-12">
                    {foreach from=$unq_target_code item=unq_tc}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_filter[target_code][{$unq_tc}]" {if $logging_filter.target_code.$unq_tc eq 1}checked="checked"{/if}/>{$unq_tc}
                        </label>
                    </div>
                    {/foreach} 
                </div>
    </div>
    <div class="form-group">
                <label class="col-xs-12">Session id:</label>
                <div class="col-xs-12">
                	<input type="text" class="form-control" value="{$logging_filter.cwsid}" name="logs_filter[cwsid]" />
				</div>
    </div>
    <div class="form-group">
                <label class="col-xs-12">REFERER:</label>
                <div class="col-xs-12">
					<input type="text" class="form-control" value="{$logging_filter.HTTP_REFERER}" name="logs_filter[HTTP_REFERER]" />
				</div>
    </div>
    <div class="form-group">
                <label class="col-xs-12">REDIRECT URL:</label>
                <div class="col-xs-12">
                	<input type="text" class="form-control" value="{$logging_filter.REDIRECT_URL}" name="logs_filter[REDIRECT_URL]" />
				</div>
    </div>

    <div class="form-group">
                <label class="col-xs-12">Display columns:</label>
                <div class="col-xs-12">
                    {foreach from=$log_columns item=lcol key=lcolname}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" name="logs_cols[{$lcolname}]" {if $lcol.fixed or $lcol.display}checked="checked"{/if} {if $lcol.fixed}disabled="disabled"{/if}/>{$lcol.title}&nbsp;
                        </label>
                    </div>
                    {/foreach} 
                </div>
    </div>
</div>
<div class="buttons">
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('filter_logs_form');" style="btn-danger push-20 push-5-r"}
</div>
</form>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block }

{capture name=block2}
{include file='common/navigation.tpl'}

<!--<div class="box" width="100%">-->
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
{foreach from=$log_columns item=lcol key=lcolname}
{if $lcol.fixed or $lcol.display}
<th>{include file="admin/main/sortby_link.tpl" _search=$logging_search title=$lcol.title fname=$lcolname}</th>
{/if}
{/foreach}
</tr>
</thead>
{foreach from=$logged_data item=l_data}
<tr class="{cycle values=",cycle"}" valign="top">
{if $log_columns.current_area.fixed or $log_columns.current_area.display}
<td>{$l_data.current_area}</td>
{/if}
{if $log_columns.date.fixed or $log_columns.date.display}
<td>{$l_data.date|date_format:$config.Appearance.datetime_format}</td>
{/if}
{if $log_columns.is_logged.fixed or $log_columns.is_logged.display}
<td align="center">{if $l_data.customer_id}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
{/if}
{if $log_columns.REQUEST_URI.fixed or $log_columns.REQUEST_URI.display}
<td nowrap="nowrap">
{assign var="fld_name" value='REQUEST_URI'}
{if $l_data.$fld_name ne ''}{assign var="htr_tr" value=$l_data.$fld_name|truncate:50:'...'}{if $htr_tr ne $l_data.$fld_name}<span class="lng_tooltip" title="{$l_data.$fld_name}">{$htr_tr}</span>{else}{$htr_tr}{/if}{/if}
</td>
{/if}
{if $log_columns.REQUEST_METHOD.fixed or $log_columns.REQUEST_METHOD.display}
<td align="center">{$l_data.REQUEST_METHOD}</td>
{/if}
{if $log_columns.GET_POST.fixed or $log_columns.GET_POST.display}
<td nowrap="nowrap">
{assign var="fld_name" value='GET_POST'}
{if $l_data.$fld_name ne ''}{assign var="htr_tr" value=$l_data.$fld_name|@debug_print_var|truncate:50:'...'}{if $htr_tr ne $l_data.$fld_name}<span class="lng_tooltip" title="{$l_data.$fld_name|@debug_print_var}">{$htr_tr}</span>{else}{$htr_tr}{/if}{/if}
</td>
{/if}
{if $log_columns.target_code.fixed or $log_columns.target_code.display}
<td>{$l_data.target_code}</td>
{/if}
{if $log_columns.cwsid.fixed or $log_columns.cwsid.display}
<td nowrap="nowrap">{assign var="cwsid" value=$l_data.cwsid}
<span class="lng_tooltip" title="{$sess_data.$cwsid|@debug_print_var}">{$l_data.cwsid}</span></td>
{/if}
{if $log_columns.HTTP_REFERER.fixed or $log_columns.HTTP_REFERER.display}
<td>
{assign var="fld_name" value='HTTP_REFERER'}
{if $l_data.$fld_name ne ''}{assign var="htr_tr" value=$l_data.$fld_name|truncate:50:'...'}{if $htr_tr ne $l_data.$fld_name}<span class="lng_tooltip" title="{$l_data.$fld_name}">{$htr_tr}</span>{else}{$htr_tr}{/if}{/if}
</td>
{/if}
{if $log_columns.REDIRECT_URL.fixed or $log_columns.REDIRECT_URL.display}
<td>
{assign var="fld_name" value='REDIRECT_URL'}
{if $l_data.$fld_name ne ''}{assign var="htr_tr" value=$l_data.$fld_name|truncate:30:'...'}{if $htr_tr ne $l_data.$fld_name}<span class="lng_tooltip" title="{$l_data.$fld_name}">{$htr_tr}</span>{else}{$htr_tr}{/if}{/if}</td>
{/if}
</tr>
{/foreach}
</table>
<div class="row">
	<div class="col-xs-6">
	<form name="archive_logs_form" method="post" action="index.php?target=logging" class="form-horizontal">
<input type="hidden" name="action" value="archive_logs" />
<div class="form-group">
<div class="col-xs-12">
	<div class="checkbox">
		<label class="checkbox">
    		<input type="checkbox" value="1" name="drop_archived" />{$lng.lbl_delete_archived_entries_from_database}&nbsp;
		</label>
	</div>
</div>
</div>
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_archive_listed_log href="javascript:cw_submit_form('archive_logs_form');" style="btn-danger push-20 push-5-r"}
</form>
	</div>
	<div class="col-xs-6">
		{include file='common/navigation.tpl'}
	</div>
</div>
<!--</div>-->

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_search_results}


<div class="box" style="display:none;">
    <div class="subheader" style="margin-top:0px;">
        <span class="subheader_left">{$lng.lbl_archived_logs}</span>
    </div>
    <div class="clear"></div>
    <div class="form-group">
<div style="width:100%; overflow-y: auto; max-height: 300px" >
        <table>
{foreach from=$all_arch_files item=arch_file}
            <tr>
                <td class="td_r">
<a href='{$var_dirs_web.logs_archive}{$arch_file}'>{$arch_file}</a>
                </td>
            </tr>
{/foreach}
        </table>
</div>
    </div>
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_logging local_config='Logging'}
