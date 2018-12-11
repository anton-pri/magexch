{* Common template for AJAX responses, which suppose to update content *}
<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xml>
{foreach from=$ajax_blocks item=ajax_block}
    <{$ajax_block.id} action="{$ajax_block.action|default:'update'}" {if $ajax_block.title}title="{$ajax_block.title|escape:'html'}"{/if}><![CDATA[{if $ajax_block.template}{include file=$ajax_block.template}{/if}{$ajax_block.content}]]></{$ajax_block.id}>
{/foreach}
</xml>
