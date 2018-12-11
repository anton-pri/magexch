{if $included_tab eq 'basic' && $current_area eq 'A' && $userinfo.usertype eq 'V'}

<div class="form-group input_field_0">
    <label>Promotion Pages</label>

<select name="promopages[]" class='form-control' multiple='multiple' size='10'>
{foreach from=$promopages item=p}
<option value='{$p.contentsection_id|escape}'{if $p.selected} selected="selected"{/if}>{$p.name|escape} {if $p.active neq 'Y'}[disabled]{/if}</option>
{/foreach}
</select>

</div>

{/if}
