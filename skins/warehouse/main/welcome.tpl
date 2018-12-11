<h3>{$lng.lbl_welcome_to_the_warehouses_zone}</h3>
{capture name=section}
{$lng.txt_warehouse_zone_welcome_note}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_become_our_salesman content=$smarty.capture.section}
