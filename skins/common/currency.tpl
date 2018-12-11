{if $force_currency_symbol ne ''}{assign var="currency_symbol" value=$force_currency_symbol}{else}{assign var="currency_symbol" value=$config.General.currency_symbol}{/if}{if $force_currency_rate ne ''}{assign var="currency_rate" value=1}{else}{assign var="currency_rate" value=1}{/if}
{if $current_area eq 'C' || $force_currency_rate ne ''}{math equation="x*y" x=$value y=$currency_rate assign="value"}{/if}
{if $current_area eq 'C' && !$user_account.show_prices}
{$lng.lbl_logon_to_see_prices}
{else}
{if $display_sign}{if $value gte 0 and $display_sign eq 1}+{elseif $value lt 0}-{/if}{/if}{$currency_symbol}{$value|abs_value|formatprice}{/if}
