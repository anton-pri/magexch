<!-- cw@manufacturer_script [ -->

<script type="text/javascript">
    {literal}
    $(document).ready(function(){

 $('.manufacturers').listnav({
     includeNums:false,
     noMatchText:'There are no matching entries.',
     onClick: function(letter){
         $('#manuf_nav_btn_prev span').html($('.ln-selected').prevAll('a').first().html());
         $('#manuf_nav_btn_next span').html($('.ln-selected').nextAll('a').first().html());
     }
 });
        $(".ln-disabled").each(function() {
            var $this = $(this);
            $this.replaceWith($("<div class='"+$(this).attr("class")+"'>" + $this.html() + "</div>"));
        });
        $('#manuf_nav_btn_next span').html($('.ln-selected').nextAll('a').first().html());
    })

    function prevNextManufLetter(prev_next){
        if(prev_next=='prev')
            letter = $('.ln-selected').prevAll('a').first().html().toLowerCase();
        if(prev_next=='next'){
            letter = $('.ln-selected').nextAll('a').first().html().toLowerCase();

        }
        if(prev_next=='all')
            letter = 'all';

        $('.ln-letters .'+letter).click();
        $('#manuf_nav_btn_prev span').html($('.ln-selected').prevAll('a').first().html());
        $('#manuf_nav_btn_next span').html($('.ln-selected').nextAll('a').first().html());

    }
    {/literal}
</script>
<!-- cw@manufacturer_script ] -->

{capture name=section}
    {include file="common/navigation.tpl"}
<!-- cw@manufacturer_nav [ -->

<div class="navigation_pages manufact">
    <a class='page_arrow' href='javascript: void(0);' onclick="javacsript:prevNextManufLetter('prev');">
        <div id="manuf_nav_btn_prev"><span>&nbsp;</span>&nbsp;<img src="{$ImagesDir}/larrow_grey.gif" alt="{$lng.lbl_prev_page|escape}" /></div>
    </a>
    {*<a class="page"       href='javascript: void(0);' onclick="javacsript:prevNextManufLetter('all');" title="{$lng.lbl_view_all}">{$lng.lbl_view_all} </a>*}
    <a class='page_arrow float-right' href='javascript: void(0);' onclick="javacsript:prevNextManufLetter('next');">
        <div id="manuf_nav_btn_next"><img src="{$ImagesDir}/rarrow_grey.gif" alt="{$lng.lbl_next_page|escape}" />&nbsp;<span>&nbsp;</span></div>
    </a>
</div>
<div class="clear"></div>

<!-- cw@manufacturer_nav ] -->

<!-- cw@manufacturers [ -->

<ul  id="manufacturers_list" class="manufacturers" >
{foreach from=$manufacturers item=v}
<!-- cw@manufacturer_item [ -->
<li class="manufacturer_info">
       {if $v.image}<div class="images">{include file='main/images/image.tpl' image=$v.image}</div>{/if}
    	<a href="{pages_url var='manufacturers' manufacturer_id=$v.manufacturer_id}" class="title">{$v.manufacturer|escape} {if $v.products_count > 0}({$v.products_count}){/if}</a>
</li>
<!-- cw@manufacturer_item ] -->

{/foreach}
</ul>
<!-- cw@manufacturers ] -->

{/capture}

{include file="common/section.tpl" is_dialog=1 title=$lng.lbl_manufacturers content=$smarty.capture.section additional_class="manufacturers_list"}