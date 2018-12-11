{if $all_languages_cnt gt 1}
<div class="selector_language" align="right">
{if !$without_label}<span>{$lng.lbl_language}:</span>{/if}
    <select name="esl" onchange="javascript: self.location='{$script|amp}&amp;els='+this.value"{if $disabled} disabled{/if}>
{foreach from=$all_languages key=code item=language}
<option value="{$code}"{if $edited_language eq $code} selected="selected"{/if}>{$language.language}</option>
{/foreach}
    </select>
</div>
{/if}
