<p>
Following files from main skin [{$default_skin}] were analyzed and processed in alternative skin [{$altskin}]
</p>
<pre>
{foreach from=$result item=file}
{$file|replace:'[*]':'<font color=green>[*]</font>'|replace:'[!]':'<font color=red>[!]</font>'}
{/foreach}
</pre>
<br />
<a href='index.php?target=domains'>Return back to domains</a>
