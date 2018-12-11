{assign var='alt_template_displayed' value=false}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='AB' param2=$page_data.contentsection_id param3='magexch_xc_page_template' assign='magexch_xc_page_template'}
{if $magexch_xc_page_template ne ''}
  {tunnel func='magexch_load_custom_template_content' via='cw_call' param1=$magexch_xc_page_template assign='magexch_custom_template_content'}
  {if $magexch_custom_template_content ne ''}
    {eval var=$magexch_custom_template_content}
    {assign var='alt_template_displayed' value='true'}
  {/if}
{/if}
{if !$alt_template_displayed}
{include file="../skins/customer/index.tpl"}
{/if}
