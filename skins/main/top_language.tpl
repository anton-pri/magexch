{if $all_languages_cnt gt 1}
{if !$without_label}<span>{$lng.lbl_language}:</span>{/if}
{include file='main/select/language_flag.tpl'}
{/if}
