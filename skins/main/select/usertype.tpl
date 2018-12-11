{if $mode eq "static"}
	{if $value ne '' && $limit ne ''}
		{if $limit eq 'B'}
			{if $value eq 'B'}{$lng.lbl_user_B}{/if}
		{/if}
		{if $limit eq 'C' || $limit eq 'I' || $limit eq 'O' || $limit eq 'S' || $limit eq 'G'}
			{if $value eq 'C'}{$lng.lbl_user_C}{/if}
			{if $value eq 'R'}{$lng.lbl_user_R}{/if}
		{/if}
		{if $limit eq 'D'}
			{if $value eq 'P'}{$lng.lbl_user_P}{/if}
		{/if}
		{if $limit eq 'P' || $limit eq 'R' || $limit eq 'Q'}
			{if $value eq 'S'}{$lng.lbl_user_S}{/if}
		{/if}
	{else}
		{$lng.lbl_not_set}
	{/if}
{else}
	<select name="{$name}"{if $onchange} onchange="javascript: {$onchange}"{/if}>
	{if $limit eq 'B'}
	<option value="B"{if $value eq 'B'} selected{/if}>{$lng.lbl_user_B}</option>
	{/if}
	{if $limit eq 'C' || $limit eq 'I' || $limit eq 'O' || $limit eq 'S' || $limit eq 'G'}
	<option value="C"{if $value eq 'C'} selected{/if}>{$lng.lbl_user_C}</option>
	<option value="R"{if $value eq 'R'} selected{/if}>{$lng.lbl_user_R}</option>
	{/if}
	{if $limit eq 'D'}
	<option value="P"{if $value eq 'P'} selected{/if}>{$lng.lbl_user_P}</option>
	{/if}
	{if $limit eq 'P' || $limit eq 'R' || $limit eq 'Q'}
	<option value="S"{if $value eq 'S'} selected{/if}>{$lng.lbl_user_S}</option>
	{/if}
	</select>
{/if}