{if $alt eq ''}{tunnel func='cw_clean_url_alt_tags' via='cw_call' param1=$item_id|default:$image.id param2=$image_type|default:$image.in_type assign='alt'}{/if}{$alt|escape}
