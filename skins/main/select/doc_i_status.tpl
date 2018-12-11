{if $extended eq "" and $status eq ""}
	{$lng.lbl_wrong_status}
{elseif $mode eq "select"}
	<select name="{$name}" {$extra}>
		{if $extended ne ""}<option value=""></option>{/if}
		<option value="Q"{if $status eq "Q"} selected="selected"{/if}>{$lng.lbl_pending}</option>
		<option value="T"{if $status eq "T"} selected="selected"{/if}>{$lng.lbl_processing}</option>
		<option value="D"{if $status eq "D"} selected="selected"{/if}>{$lng.lbl_declined}</option>
		<option value="P"{if $status eq "P"} selected="selected"{/if}>{$lng.lbl_approved}</option>
		<option value="F"{if $status eq "F"} selected="selected"{/if}>{$lng.lbl_expired}</option>
		<option value="C"{if $status eq "C"} selected="selected"{/if}>{$lng.lbl_paid}</option>
	</select>
{elseif $mode eq "static"}
	{if $status eq "Q"}
		{$lng.lbl_pending}
	{elseif $status eq "T"}
		{$lng.lbl_processing}
	{elseif $status eq 'D'}
		{$lng.lbl_declined}
	{elseif $status eq 'P'}
		{$lng.lbl_approved}
	{elseif $status eq "C"}
		{$lng.lbl_paid}
	{elseif $status eq 'F'}
		{$lng.lbl_expired}
	{/if}
{/if}
