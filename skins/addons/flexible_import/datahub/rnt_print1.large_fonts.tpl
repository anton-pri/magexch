<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head>
<style>
{if $x_k eq ''}
{assign var="x_k" value="1.049"}
{assign var="x_k" value="1"}
{/if}
{if $y_k eq ''}
{assign var="y_k" value="1.041667"}
{assign var="y_k" value="1"}
{/if}
{literal}
body{
  font-family: Arial, Helvetica, sans-serif;
  padding:0;
  margin: 0;
}
.letter{
  width: {/literal}{math equation="11*x" x=$x_k}{literal}in;
  overflow: hidden;
  padding-top: 1px;
}
.tag{
  width: {/literal}{math equation="3*x" x=$x_k}{literal}in;
  height: {/literal}{math equation="3.5*x" x=$y_k}{literal}in;
  float: left;
  text-align: center;
  outline: 1px solid #666;
  margin-left: 1px;
  margin-top: 0px;
  margin-bottom: 1px;
}

.tag.second{
  margin-left: 1px;
}
.tag > div{
   padding: 0.1in;
}

.sitename{
  font-size: 13pt;
  padding-bottom: 0.05in;
}

.winename{
  font-size: 18pt;
  color: #003471;
}
.price{
  font-size: 20pt;
  color: #9e0b0f;
}

.rating{
  font-size: 12pt;
  padding-top: 0.2in;
}

.rating b{
  font-size: 13pt;
}

.break{
    page-break-before: always;
}
{/literal}
</style>
</head>
<body>
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
			<div class="price">{include file='common/currency.tpl' value=$prn_itm.$keyname}</div>
{assign var="keyname" value='ID'}
                        <div class="sitename">SKU: {$prn_itm.$keyname}</div>
			<div class="rating">

                            {if $prn_itm.max_rating ne '' && $prn_itm.max_rating.0 gt 0}
                            {if $prn_itm.max_rating.0 gt 1}<b>Rated {$prn_itm.max_rating.0}.</b> {/if}{$prn_itm.max_rating.1|truncate:$review_text_limit:'...'} -
                            {$prn_itm.max_rating.2}
                            {elseif $prn_itm.just_not_empty_review ne ''}    
                            {$prn_itm.just_not_empty_review}<br /> - {$prn_itm.just_not_empty_review_magazine}
                            {elseif $prn_itm.LongDesc ne ''}
                            {$prn_itm.LongDesc|truncate:$descr_text_limit:'...'}       
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
</body>
</html>
