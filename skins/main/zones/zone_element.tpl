<textarea cols="40" rows="{$box_size|default:3}" style="width: 100%;" name="{$name}" class="form-control">
{section name=id loop=$zone_elements}
{if $zone_elements[id].field_type eq $field_type}
{$zone_elements[id].field|escape}
{/if}
{/section}
</textarea>


