{if $hot_deals_week}
<script language="javascript">
var current_page = 1;
var total_pages = parseInt('{$week_navigation.total_pages}');
var week_pages = new Array();
var max_products_per_page = {$config.special_sections.hot_deals_week|default:3};
{literal}
function next_week_page() {
    if (current_page < total_pages-1) current_page += 1;
    else current_page = 1;
    set_week_page(current_page);
}

function prev_week_page() {
    if (current_page > 1) current_page -= 1;
    else current_page = total_pages-1;
    set_week_page(current_page);
}

function set_week_page(page) {
    current_page = page;
   
    var index = 1;
    for(i in week_pages) {
        disp = 'none';
        if (index >= current_page && index <= current_page+max_products_per_page-1) disp = ''
        document.getElementById(week_pages[i]).style.display = disp;
        if (index == current_page+max_products_per_page-1)
            document.getElementById(week_pages[i]).className='prod_border last';
        else
            document.getElementById(week_pages[i]).className='prod_border';
        index++;
    }
}

{/literal}
</script>
{capture name=section}
{if $week_navigation.total_pages gt 2}
<a href="javascript:prev_week_page(); void(0);" class="deals_week_left"></a>
<a href="javascript:next_week_page(); void(0);" class="deals_week_right"></a>
{/if}

{include file='customer/special_sections/deals_products.tpl' products=$hot_deals_week is_week=1}

{/capture} 
{include file='common/section.tpl' title=$lng.lbl_deal_week content=$smarty.capture.section style='deals_week' is_dialog=1}
<script language="javascript">
set_week_page(1);
</script>
{/if}
