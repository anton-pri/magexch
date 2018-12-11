{if $extended eq "" and $status eq "" and !$multiple}
{$lng.lbl_wrong_status}
{elseif ($mode eq "select" || $mode eq "static")}{cw_order_statuses mode=$mode selected=$status name=$name extra=$extra extended=$extended multiple=$multiple normal_array=$normal_array}{/if}
