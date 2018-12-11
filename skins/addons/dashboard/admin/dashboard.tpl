<div class="box">
<form method='post'>
    <input type='hidden' name='action' value="update" />
<table width='96%' align='center'>
    <tr class='header'>
        <td>pos</td>
        <td>Title</td>
        <td>Description</td>
        <td>Active</td>
    </tr>

{foreach from=$dashboard item=dash_section key=name}
    <tr class='{cycle values='cycle,'}'>
        <td><input type="text" value='{$dash_section.pos}' name='dashboard[{$name}][pos]' class='micro' size='3' /></td>
        <td>{$dash_section.title}</td>
        <td>{$dash_section.description}</td>
        <td><input type="checkbox" value='1' {if $dash_section.active}checked='checked'{/if} name='dashboard[{$name}][active]' /></td>
    </tr>
{/foreach}
</table>
    <input type="submit" value="Update" />
</form>
</div>
