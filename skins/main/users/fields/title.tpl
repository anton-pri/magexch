{if $profile_fields.address.title.is_avail}
<div class="input_field_{$profile_fields.address.title.is_required}">
    <label>{$lng.lbl_title}</label>
    {include file="main/select/user_title.tpl" name="`$name_prefix`[title]" field=$address.titleid}
</div>
{/if}
