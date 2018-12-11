{if $count_products}
{literal}
<script type="text/javascript">
var count_products ='{/literal}{$count_products}';{literal}
var current_location ='{/literal}{$current_location}';{literal}
$("#content_menu_cart").append("<li><a href=\""+current_location+"/index.php?target=new_product\">"+count_products+"</a></li>");
</script>
{/literal}
{/if}