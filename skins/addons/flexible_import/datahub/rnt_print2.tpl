<html>
<head>
<style>
{assign var="x_k" value="1.049"}
{assign var="y_k" value="1.041667"}
{assign var="x_k" value=$config.flexible_import.flex_import_large_tag_width_coeff|default:1}
{assign var="y_k" value=$config.flexible_import.flex_import_large_tag_height_coeff|default:1}
{literal}
@font-face {
    font-family: 'MyriadProRegular';
    src: url('/skins/addons/flexible_import/datahub/fonts/myriadpro-regular.eot');
    src: url('/skins/addons/flexible_import/datahub/fonts/myriadpro-regular.eot') format('embedded-opentype'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-regular.woff') format('woff'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-regular.ttf') format('truetype'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-regular.svg#MyriadProRegular') format('svg');
}

@font-face {
    font-family: 'yanone_kaffeesatzlight';
    src: url('/skins/addons/flexible_import/datahub/fonts/yanonekaffeesatz-light-webfont.eot');
    src: url('/skins/addons/flexible_import/datahub/fonts/yanonekaffeesatz-light-webfont.eot?#iefix') format('embedded-opentype'),
         url('/skins/addons/flexible_import/datahub/fonts/yanonekaffeesatz-light-webfont.woff') format('woff'),
         url('/skins/addons/flexible_import/datahub/fonts/yanonekaffeesatz-light-webfont.ttf') format('truetype'),
         url('/skins/addons/flexible_import/datahub/fonts/yanonekaffeesatz-light-webfont.svg#yanone_kaffeesatzlight') format('svg');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'MyriadProBold';
    src: url('/skins/addons/flexible_import/datahub/fonts/myriadpro-bold.eot');
    src: url('/skins/addons/flexible_import/datahub/fonts/myriadpro-bold.eot') format('embedded-opentype'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-bold.woff') format('woff'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-bold.ttf') format('truetype'),
         url('/skins/addons/flexible_import/datahub/fonts/myriadpro-bold.svg#MyriadProBold') format('svg');
}


body{
  font-family: MyriadProRegular, Arial, Helvetica, sans-serif;
  padding:0;
  margin: 0;
}

.letter{
  width: {/literal}{math equation="8.5*x" x=$x_k}{literal}in;
  overflow: hidden;
  padding-top: 1px;
  padding-left: 1px;

}

.table > div{
  padding: 0 0.1in;
  height: {/literal}{math equation="4.5*x" x=$y_k}{literal}in;


}
.table{
  width: {/literal}{math equation="4.125*x" x=$x_k}{literal}in;
  height: {/literal}{math equation="5.375*x" x=$y_k}{literal}in;
  float: left;
  text-align: center;
  margin: 0;
  border: 1px solid #666;
  margin-top: -1px;
  background:#fff;
  margin-left: -1px;

}

.class1, .class2{
  margin-left: 0px;
  border-left: 0;
}

.title{
  font-size: 25pt;
  padding-top: 0.1in;
  padding-bottom: 0.2in;
}

.price{
  font-size: 40pt;
  color: #9e0b0f;
  font-family: MyriadProBold;
  padding-bottom: 10px;
  width: 100%;
}
.list_price {
  font-size: 30pt;
  line-height: 20pt;
}
.break{
    page-break-after: always;
}

span.red{
    font-weight: bold;
	color: #9e0b0f;
	font-size: 14pt;
}

.descr{
  font-family: yanone_kaffeesatzlight;
  font-size: 13pt;
  pdding-bottom: 0.1in;
}

.price sup{
  font-size: 20pt;
}
.bottle{
  margin-top: 1px;
}

.more{
  width: 100%;
}

.height{
  height: 3in;
}
{/literal}
{$config.flexible_import.flex_import_large_tag_extra_css}
</style>
</head>
<body>
<div class="letter">
{assign var="breakCounter" value=0}
{assign var="review_text_limit" value=$config.flexible_import.flex_import_large_tag_text_limit}
{assign var="descr_text_limit" value=$config.flexible_import.flex_import_large_tag_text_limit+40}

{foreach from=$print_items item=prn_itm name=tags}
        <div class="table {if $smarty.foreach.tags.iteration is div by 2}second{/if}">
		  <div>
		    <div class="bottle"><img src="/skins/addons/flexible_import/datahub/images/bottle.png" alt="" style="width: 3.4in;" /></div>
                        {assign var="keyname" value='Item Description'}
                     <div class="height">
                        <div class="title">{$prn_itm.$keyname}</div>
			   <div class="descr">
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


                        {assign var="keyname" value='Regular Price'}
                        <!-- List price: {$prn_itm.list_price} -->
                        <div class="price list_price">
			{if $sales_tag && $prn_itm.list_price && $prn_itm.list_price gt $prn_itm.$keyname}
			  <s>{include file='common/currency.tpl' value=$prn_itm.list_price}</s>
			{else}
			  &nbsp;
			{/if}
			</div>
                        <div class="price">{include file='common/currency.tpl' value=$prn_itm.$keyname}</div>
			<div class="more">Find More at <span class="red">SaratogaWine.com</span></div>
      	  </div>
		</div>
                {math equation="x+1" x=$breakCounter assign="breakCounter"}
                {if $breakCounter eq 4 && !$smarty.foreach.tags.last}
                     <p style="page-break-before: always; clear: both;"> </p>
                    {assign var="breakCounter" value=0}
                {/if}
{/foreach}
</div>
</body>
</html>
