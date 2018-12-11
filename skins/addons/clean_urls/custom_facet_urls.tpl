{capture name=section}

{*include file='common/page_title.tpl' title=$lng.lbl_custom_facet_urls*}
{capture name=block}

<div class="clear"></div>

{* form without action should post to the same url where appears, e.g. on manufacturer page *}
<form  method="post" name="custom_facet_urls_filter_form" class="form-horizontal">
	<input type="hidden" name="action" value="search_facet" />
	<div class="box">

		{foreach from=$filter_options item=option}
		<div class="form-group" >

						<label class="col-xs-12">
							{$option.name}
						</label>

						{foreach from=$option.options item=v}
						<div class="attribute_item col-xs-12">
							<label>
								<input type="checkbox" class="attribute_option" name="attribute_option[]" value="{$v.attribute_value_id}" {if $v.checked}checked="" {/if} />
								{$v.name}&nbsp;
							</label>
						</div>
						{/foreach}

		</div>
		{/foreach}

		<div class="form-group">
			<label class="col-xs-12">{$lng.lbl_search_for_pattern}:</label>
			<div class="col-xs-12 col-md-6"><input class="form-control" type="text" name="attribute_option_substring" value="{$search_prefilled.substring}" /></div>
		</div>
	</div>
    <div class="buttons">
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search onclick="cw_submit_form('custom_facet_urls_filter_form');" style="btn-green push-5-r push-20"}
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_reset onclick="document.forms.custom_facet_urls_filter_form.action.value='reset'; cw_submit_form('custom_facet_urls_form', 'reset');" style="btn-danger push-20"}
    </div>
</form>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$lng.lbl_filter_options}
<div class="clear"></div>


{capture name=section}

{include file='common/navigation_counter.tpl'}

{if $navigation.total_items gt 4}
	{include file='common/navigation.tpl'}
{/if}

{* form without action should post to the same url where appears, e.g. on manufacturer page *}
<form method="post" name="custom_facet_urls_form">
	<input type="hidden" name="action" value="" />
	<div class="box">
		<table width="100%" class="table table-striped dataTable vertical-center">
		<thead>
			<tr>
				{if $custom_facet_urls}<th width="5%"  class="text-center"><input type='checkbox' class='select_all' class_to_select='custom_facet_url_item' /></th>{/if}
                <th width="10%" class="text-center">
                    {strip}
                        {if $search_prefilled.sort_field eq 'title'}
                            <img src="{$ImagesDir}/r_{if $search_prefilled.sort_direction}bottom{else}top{/if}.png" class="sorting" alt="" />
                        {/if}
                        <a href="index.php?target={$current_target}&amp;sort=title&amp;sort_direction={if $search_prefilled.sort_field eq 'title'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_title}</a>
                    {/strip}
                </th>
				<th width="15%">
					{strip}
						{if $search_prefilled.sort_field eq 'custom_facet_url'}
							<img src="{$ImagesDir}/r_{if $search_prefilled.sort_direction}bottom{else}top{/if}.png" class="sorting" alt="" />
						{/if}
						<a href="index.php?target={$current_target}&amp;sort=custom_facet_url&amp;sort_direction={if $search_prefilled.sort_field eq 'custom_facet_url'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_custom_facet_url}</a>
					{/strip}
				</th>
				<th width="40%">
					{strip}
						{if $search_prefilled.sort_field eq 'clean_urls'}
							<img src="{$ImagesDir}/r_{if $search_prefilled.sort_direction}bottom{else}top{/if}.png" class="sorting" alt="" />
						{/if}
						<a href="index.php?target={$current_target}&amp;sort=clean_urls&amp;sort_direction={if $search_prefilled.sort_field eq 'clean_urls'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_clean_urls_combination}</a>
					{/strip}
				</th>
				<th width="30%">{$lng.lbl_options_combination}</th>
			</tr>
			</thead>
			{if $custom_facet_urls}
			{foreach from=$custom_facet_urls item=v}
			<tr{cycle values=", class='cycle'"}>
				<td align="center"><input type="checkbox" name="to_delete[{$v.url_id}]" class="custom_facet_url_item" /></td>
                <td><a href="index.php?target=custom_facet_urls&mode=details&custom_facet_url_id={$v.url_id}">{$v.title}</a></td>
				<td><a href="index.php?target=custom_facet_urls&mode=details&custom_facet_url_id={$v.url_id}">{if $v.multi_url_count eq 1}{$v.custom_facet_url}{else}{$v.multi_url_count} combinations{/if}</a></td>
				<td><div style="max-height:50px; overflow: auto; width: 95%"><a href="index.php?target=custom_facet_urls&mode=details&custom_facet_url_id={$v.url_id}">{$v.clean_urls}</a></div></td>
				<td><div style="max-height:50px; overflow: auto; width: 95%">{$v.options_combination}</div></td>
			</tr>
			{/foreach}
			{else}
			<tr>
				<td colspan="5" align="center">{$lng.txt_no_custom_facet_urls}</td>
			</tr>
			{/if}
		</table>
	</div>
</form>

{if $navigation.total_items gt 4}
	{include file='common/navigation.tpl'}
{/if}
<div class="buttons">
{if $custom_facet_urls}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('custom_facet_urls_form', 'delete');" style="btn-danger push-5-r push-20"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=custom_facet_urls&mode=add" style="btn-green push-5-r push-20"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_search_results section_id='facet_urls_list'}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_custom_facet_urls}
