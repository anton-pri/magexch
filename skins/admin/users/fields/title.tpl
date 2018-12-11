{if $profile_fields.address.title.is_avail}
<div class="form-group input_field_{$profile_fields.address.title.is_required}">
    <label class="col-xs-12">{$lng.lbl_title}</label>
    <div class="col-xs-12">{include file="admin/select/user_title.tpl" name="`$name_prefix`[title]" field=$address.titleid}</div>
</div>
{/if}
