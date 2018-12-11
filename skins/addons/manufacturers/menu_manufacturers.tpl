{if $addons.manufacturers and $config.manufacturers.manufacturers_menu eq 'Y'}
{select_manufacturer_menu assign='manufacturers_menu'}
{if $manufacturers_menu}
{capture name=menu}

{if $config.manufacturers.view_list_manufacturers eq 0}
    <select onchange="javascript:document.location.href = 'index.php?target=manufacturers&manufacturer_id='+this.value;" class="w100">
    <option value="0">{$lng.lbl_choose}</option>
    {section name=mid loop=$manufacturers_menu}
    <option value="{$manufacturers_menu[mid].manufacturer_id}" {if $manufacturers_menu[mid].manufacturer_id eq $manufacturer_id}selected{/if}>{$manufacturers_menu[mid].manufacturer}</option>
    {/section}
    </select>
{elseif $config.manufacturers.view_list_manufacturers eq 1}
    <div style="text-align: left">
        {assign var=count_visible value=4}

        {section name=mid loop=$manufacturers_menu max=$count_visible}
            <a href='{pages_url var="manufacturers" manufacturer_id=$manufacturers_menu[mid].manufacturer_id}'>{$manufacturers_menu[mid].manufacturer}</a>
        {/section}

        {if $manufacturers_menu|@count gt $count_visible}
            <div id="manufacturers_show_more">
                <a href="javascript: $('#manufacturers_more_links').show();$('#manufacturers_show_more').hide(); void(0);">{$lng.lbl_more}</a>
            </div>
            <div style="display: none" id="manufacturers_more_links">
                {section name=mid loop=$manufacturers_menu start=$count_visible}
                    <a href='{pages_url var="manufacturers" manufacturer_id=$manufacturers_menu[mid].manufacturer_id}'>{$manufacturers_menu[mid].manufacturer}</a>
                {/section}
            </div>
        {/if}
    </div>
{/if}

{/capture}
{include file='common/menu.tpl' title=$lng.lbl_manufacturers content=$smarty.capture.menu style='manuf'}
{/if}
{/if}
