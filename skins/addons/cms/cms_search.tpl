{*
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/addons/cms/func.js"></script>

{*include file='common/page_title.tpl' title=$lng.lbl_cms_search*}
{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}{if $mode ne ""}&amp;mode={$mode}{/if}" method="post" name="filterContentSectionsForm">
  <input type="hidden" name="action" value="" />
  <input type="hidden" name="page" value="{$page|default:"1"}" />
<div class="form-horizontal">

      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_type}:</label>
      
		<div class="col-xs-12">      
        <select name="filter[type][]" multiple class="form-control">
          <option value="html" {foreach from=$contentsections_filter.type item=cs_filter_type}{if $cs_filter_type eq "html"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_type_embedded_html}
          </option>
          <option value="image" {foreach from=$contentsections_filter.type item=cs_filter_type}{if $cs_filter_type eq "image"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_type_embedded_image}
          </option>
          <option value="staticpage" {foreach from=$contentsections_filter.type item=cs_filter_type}{if $cs_filter_type eq "staticpage"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_type_separate_static_page}
          </option>
          <option value="staticpopup" {foreach from=$contentsections_filter.type item=cs_filter_type}{if $cs_filter_type eq "staticpopup"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_type_static_content_popup}
          </option>
        </select>
        </div>
      </div>

      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_service_code}:</label>
      <div class="col-xs-12">   
      	<input type="text" class="form-control" name="filter[service_code]" maxlength="64" value="{$contentsections_filter.service_code}" style="width: 300px;" />
      </div>
      </div>

      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_title}:</label>
      <div class="col-xs-12"> 
      	<input type="text" class="form-control" name="filter[name]" maxlength="64" value="{$contentsections_filter.name}" style="width: 300px;" />
      </div>	
      </div>
{* alt tag is attribute now, change cms search and reenable this
      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_alt_text}:</label>
		<div class="col-xs-12"> 
      		<input type="text"  class="form-control" name="filter[alt]" maxlength="128" value="{$contentsections_filter.alt}" style="width: 300px;" />
		</div>
      </div>
*}
      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_open_link_in}:</label>
      <div class="col-xs-12">
        <select name="filter[target][]" multiple size="2"  class="form-control">
          <option value="_self" {foreach from=$contentsections_filter.target item=cs_target}{if $cs_target eq "_self"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_target_same_window}
          </option>
          <option value="_blank" {foreach from=$contentsections_filter.target item=cs_target}{if $cs_target eq "_blank"}selected="selected"{/if}{/foreach}>
            {$lng.lbl_cs_target_new_window}
          </option>
        </select>
      </div>  
      </div>

      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_url}:</label>
      <div class="col-xs-12">
        <input type="text"  class="form-control" name="filter[url]" maxlength="128" value="{$contentsections_filter.url}" style="width: 300px;" />
      </div>  
      </div>

      <div class="form-group right"><label class="col-xs-12">{$lng.lbl_cs_layout_of_multiple_sections}:</label>
      <div class="col-xs-12">
        <select name="filter[skin][]"  class="form-control" multiple>
          {foreach from=$skins item=skin}
             <option value="{$skin}"{foreach from=$contentsections_filter.skin item=cs_skin}{if $cs_skin eq $skin} selected="selected"{/if}{/foreach}>{$skin}</option>
          {/foreach}
        </select>
      </div>  
      </div>
   
      <div class="form-group right"><label class="col-xs-12" for="filter_date">{$lng.lbl_date}:</label>
      <div class="form-inline col-xs-12">
        <div class="form-group">{include file="main/select/date.tpl" name="filter[start_filter_date]" value=$contentsections_filter.start_date}</div>
        <div class="form-group"> - </div>
        <div class="form-group">{include file="main/select/date.tpl" name="filter[end_filter_date]" value=$contentsections_filter.end_date}</div>
      </div> 
      </div>

    <div class="form-group right"><label class="col-xs-12">{$lng.lbl_restrict_clean_urls}:</label>
    <div class="col-xs-12">
        <table>
           <tr>
               <td id="content_section_clean_urls_box_0"><input class="form-control" type="text" id="content_section_clean_urls_value_0" name="content_section_clean_urls[0][value]" maxlength="64" value="" /></td>
               <td id="content_section_clean_urls_box_1"></td>
               <td id="content_section_clean_urls_add_button" class=>{include file='main/multirow_add.tpl' mark='content_section_clean_urls'}</td>
           </tr>

        </table>
        <script type="text/javascript">
            {foreach from=$contentsections_filter.clean_urls key=key item=url}
                add_inputset_preset('content_section_clean_urls', document.getElementById('content_section_clean_urls_add_button'), true,
                    [
                    {ldelim}regExp: /content_section_clean_urls_value_{$key+1}/, value: '{$url}'{rdelim},
                    ]);
            {/foreach}
        </script>
        
    </div>    
    </div>
{if $filter_restricted_attributes ne ''}
   <div class="addon_title">{$lng.lbl_cs_filter_by_restricted_attributes|default:'Filter by restricted attributes'}</div>
   <div class="form-group">
        {foreach from=$filter_restricted_attributes item=option}
            <table>
                <tr>
                    <td class="td_l">
                        <label>
                            {$option.name}:
                        </label>
                    </td>
                    <td class="td_r">
                        {if $option.options} 
                        <div style="max-height:112px; overflow-y:auto;"> 
                        {foreach from=$option.options item=v}
                        <div class="attribute_item">
                            <label class="checkbox">
                                <input type="checkbox" class="attribute_option form-control" name="restricted_attributes[{$option.attribute_id}][]" value="{$v.attribute_value_id}" {if $v.checked}checked="" {/if} />
                                {$v.name}&nbsp;
                            </label>
                        </div>
                        {/foreach}
                        </div>
                        {elseif $option.type eq 'text'}   
                        <div class="attribute_item">
                            <label class="checkbox">
                                <input type="text" class="attribute_option form-control" name="restricted_attributes[{$option.attribute_id}]" value="{$option.value}" />
                            </label>
                        </div> 
                        {/if} 
                    </td>
                </tr>
            </table>
        {/foreach}
    </div>
{/if}

	{include file='admin/attributes/object_modify.tpl'}
</div>
<div class="buttons">
	{include file="buttons/button.tpl" href="javascript: cw_submit_form('filterContentSectionsForm', 'update_filters');" button_title=$lng.lbl_search|escape acl=$page_acl style="btn btn-green push-20 push-10-r"}
	&nbsp;
	{include file="buttons/button.tpl" href="javascript: resetFilter();" button_title=$lng.lbl_reset|escape acl=$page_acl style="btn btn-danger push-20"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_cms_search}
<br />

{if $contentsections}
  {include file="addons/cms/cms_list.tpl" contentsections=$contentsections}
{/if}


<div class="clear"></div>
