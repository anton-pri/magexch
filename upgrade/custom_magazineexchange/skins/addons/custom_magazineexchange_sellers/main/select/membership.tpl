{tunnel func='cw\custom_magazineexchange_sellers\mag_membership_fees' via='cw_call' param1=$value assign=fees}
, {$config.General.currency_symbol}{$fees.item} per item / {$fees.percent}%
