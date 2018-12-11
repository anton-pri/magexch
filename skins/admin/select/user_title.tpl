{select_user_title assign="titles"}
<select class="form-control" name="{$name}"{if $readonly} disabled{/if}>
{foreach from=$titles item=v}
    <option value="{$v.title_orig|escape}"{if $field eq $v.titleid} selected="selected"{/if}>{$v.title}</option>
{/foreach}
</select>
            
