<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<addresses>
{foreach from=$addresses item=address}
    <address address_id="{$address.address_id}" is_main="{$address.main}" is_current="{$address.current}"><![CDATA[
    {if $profile_fields.address.title.is_avail}
        {$address.title}
    {/if}
    {if $profile_fields.address.firstname.is_avail}
        {$address.firstname}
    {/if}
    {if $profile_fields.address.lastname.is_avail}
        {$address.lastname}
    {/if}
    {if $profile_fields.address.address.is_avail || $profile_fields.address.address_2.is_avail}
        {$address.address}{if $address.address_2} {$address.address_2}{/if}
    {/if}
    {if $profile_fields.address.city.is_avail}
         {$address.city}
    {/if}
    {if $profile_fields.address.county.is_avail and $config.General.use_counties eq "Y" and $address.county}
        {$address.county}
    {/if}
    {if $profile_fields.address.state.is_avail and $address.state}
        {$address.statename} {$address.state}
    {/if}
    {if $profile_fields.address.country.is_avail}
         {$address.countryname}
    {/if}
    {if $profile_fields.address.zipcode.is_avail}
        {$address.zipcode}
    {/if}
    {if $profile_fields.address.phone.is_avail}
        {$address.phone}
    {/if}
    {if $profile_fields.address.fax.is_avail}
        {$address.fax}
    {/if}]]></address>
{/foreach}
</addresses>
