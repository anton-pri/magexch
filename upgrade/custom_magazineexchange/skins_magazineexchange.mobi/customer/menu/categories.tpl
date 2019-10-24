{if $main ne "subcategories" || $cat eq $config.custom_magazineexchange.magexch_default_root_category}
  {select_categories category_id=$config.custom_magazineexchange.magexch_default_root_category|default:0 current_category_id=$cat assign='categories' images="Y"}
{else}
  {tunnel func='magexch_get_section_category_id' via='cw_call' param1=$cat assign='section_category_id'}
  {select_categories category_id=$section_category_id current_category_id=0 assign='categories' images="Y"}
{/if}

{if $vendorid}
{tunnel func='magexch_filter_categories_by_vendor' via='cw_call' param1=$categories param2=$vendorid assign='categories'}
{/if}

<div class="mobile_topmenu">
  <div class="menu_title" id="browse_sections">{if $main ne "subcategories"}{$lng.lbl_browse_main_sections_mobile}{else}{$lng.lbl_magazines_in_this_section}{/if}</div>
  <ul id="mobile_sections">
  {foreach from=$categories item=c}
    <li>
      <a href="{pages_url var='index' cat=$c.category_id}">{$c.category}</a>
    </li>
  {/foreach}
<!--{if !$vendorid}
      <li class="classifieds"><a href="Classifieds.html">{$lng.lbl_classifieds}</a></li>-->
{/if}

  </ul>
</div>

{literal}
<script type="text/javascript">
$(document).ready(function() {
  $("#browse_sections").click(function() {
    $("#mobile_sections").toggle();
    $(".menu_title").toggleClass("opened");
  });
});
</script>
{/literal}



