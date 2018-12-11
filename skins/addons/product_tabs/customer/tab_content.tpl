{tunnel func='cw_pt_get_tab_content' assign='tab_data'}{if $tab_data.parse}{eval var=$tab_data.content}{else}{$tab_data.content}{/if}
{if $tab_data.attributes}
{include file='customer/products/feature_tab.tpl' list_of_attributes=$tab_data.attributes}
{/if}
