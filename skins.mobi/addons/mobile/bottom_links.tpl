{if $mobile_select_type eq "1"}
	<a href="{$full_host}">{$lng.lbl_full_version}</a>
{elseif $mobile_host ne ""}
	<a href="{$mobile_host}">{$lng.lbl_mobile_version}</a>
{/if}