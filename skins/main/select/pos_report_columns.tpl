<select name="{$name}" id="{$name|id}">
{foreach from=$elements key=name item=lbl}
<option value="{$name}"{if $value eq $name} selected{/if}>{lng name=$lbl}</option>
{/foreach}
</select>
