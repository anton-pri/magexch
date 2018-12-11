{*
<h3>Install addons from repository</h3>

*}




{if $addonname}
<div class="box">
<iframe src='index.php?target=installmod&mod={$addonname}' frameborder='no' width='795' height='1000' scrolling='auto' vspace='10' name='installer'>
</iframe>
</div>
{else}
{*include file='common/page_title.tpl' title=$lng.lbl_install_addons*}
{capture name=section}
<p>To install a addon into this install of the CartWorks Platform, please upload the file below.</p>

<form enctype="multipart/form-data" name="package_form" method="post">

<div class="box">

<div class="input_field_0"> 
<input name='action' value='upload' type='hidden' />
<label>Addon package:</label> <input name='filename' type='file' size='40' />
</div>
<div class="buttons"><input type="submit" value="Upload" /></div>

</div>
</form>
{if $uploaded}
<p>The addon "{$uploaded}" was uploaded and installation is not completed. 
<a href='index.php?target=addons_manager&action=install'>Continue installation of "{$uploaded}"</a>
</p>
{/if}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_install_addons}

{/if}
