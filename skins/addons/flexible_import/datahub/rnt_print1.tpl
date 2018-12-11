<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head>
<style>
{if $x_k eq ''}
{*assign var="x_k" value="1.049"*}
{assign var="x_k" value="1.1556"}
{assign var="x_k" value="1"}
{/if}
{if $y_k eq ''}
{*assign var="y_k" value="1.041667"*}
{assign var="y_k" value="1.1538"}
{assign var="y_k" value="1"}
{/if}

{assign var="x_k" value=$config.flexible_import.flex_import_small_tag_width_coeff|default:1}
{assign var="y_k" value=$config.flexible_import.flex_import_small_tag_height_coeff|default:1}

{literal}
body{
  font-family: Arial, Helvetica, sans-serif;
  padding:0;
  margin: 0;
}
.letter{
 /* width: {/literal}{math equation="11*x" x=$x_k}{literal}in;*/
  overflow: hidden;
  padding-top: 1px;
}
.tag{
  width: {/literal}{math equation="3*x" x=$x_k}{literal}in;
  height: {/literal}{math equation="3.5*x" x=$y_k}{literal}in;
  float: left;
  text-align: center;
  border: 1px solid #666;
//outline: 1px solid #666;
  margin-left: 1px;
  margin-top: 0px;
  margin-bottom: 1px;
}

.tag.second{
  margin-left: 1px;
}
.tag > div{
   padding: 0.13in;
}

.sitename{
  font-size: 9pt;
  padding-bottom: 0.033in;
}

.winename{
  font-size: 13pt;
  color: #003471;
}
.price{
  font-size: 16pt;
  color: #9e0b0f;
}

.rating{
  font-size: 9pt;
  padding-top: 0.133in;
}

.rating b{
  font-size: 10pt;
}

.break{
    page-break-before: always;
}

{/literal}
{$config.flexible_import.flex_import_small_tag_extra_css}
</style>
<script type="text/javascript" src="/skins/addons/flexible_import/datahub/javascript/html2canvas.js"></script>
<script type="text/javascript">{literal}
function make_all_image() {
/*
        html2canvas(document.body, {
            onrendered: function(canvas) {
                document.body.appendChild(canvas);
                document.getElementById('all_tags').style.display = 'none';
            }
        });
*/
}
{/literal}</script>
</head>
<body onload="make_all_image()">
<div id="all_tags">
<div class="letter">
{assign var="breakCounter" value=0}
{assign var="borderCounter" value=0}
{assign var="review_text_limit" value=$config.flexible_import.flex_import_small_tag_text_limit}
{assign var="descr_text_limit" value=$config.flexible_import.flex_import_small_tag_text_limit+20}
{foreach from=$print_items item=prn_itm name=tags}
		<div class="tag {*if $smarty.foreach.tags.iteration is div by 2 or $smarty.foreach.tags.iteration is div by 3}second{/if*} class{$borderCounter}">
		  <div>
			<div class="sitename">www.SaratogaWine.com</div>
{assign var="keyname" value='Item Description'}
			<div class="winename">{$prn_itm.$keyname}</div>
                        <div class="sitename">{$prn_itm.region_data}</div>
{assign var="keyname" value='Regular Price'}
			<!-- List price: {$prn_itm.list_price} -->
			{if $sales_tag && $prn_itm.list_price && $prn_itm.list_price gt $prn_itm.$keyname}<div class="price list_price"><s>{include file='common/currency.tpl' value=$prn_itm.list_price}</s></div>{/if}
			<div class="price">{include file='common/currency.tpl' value=$prn_itm.$keyname}</div>
{assign var="keyname" value='catalog_id'}
                        <div class="sitename">SKU: {$prn_itm.$keyname}</div>
			<div class="rating">

                            {if $prn_itm.max_rating ne '' && $prn_itm.max_rating.0 gt 0}
                            {if $prn_itm.max_rating.0 gt 1}<b>Rated {$prn_itm.max_rating.0}.</b> {/if}{$prn_itm.max_rating.1|strip_tags|truncate:$review_text_limit:'...'} -
                            {$prn_itm.max_rating.2}
                            {elseif $prn_itm.just_not_empty_review ne ''}    
                            {$prn_itm.just_not_empty_review|strip_tags|truncate:$review_text_limit:'...'}<br /> - {$prn_itm.just_not_empty_review_magazine}
                            {elseif $prn_itm.LongDesc ne ''}
                            {$prn_itm.LongDesc|strip_tags|truncate:$descr_text_limit:'...'}       
                            {/if}

                        </div>
		  </div>
		</div>
                {math equation="x+1" x=$breakCounter assign="breakCounter"}
                {if $breakCounter eq 6}
                    </div>
                    <p style="page-break-before: always;clear: both;"> </p>
                    <div class="letter">

                    {assign var="breakCounter" value=0}
                {/if} 
 
                {math equation="x+1" x=$borderCounter assign="borderCounter"}
                {if $borderCounter eq 3}
                    {assign var="borderCounter" value=0}
                {/if} 
{/foreach}

</div>
</div>
</body>
</html>
