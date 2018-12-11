{section name=ri loop=$report}
{$report[ri].customer_id}{$delimiter}{$report[ri].firstname}{$delimiter}{$report[ri].lastname}{$delimiter}{$report[ri].sum_paid}{$delimiter}{$report[ri].sum_nopaid}{$delimiter}{$report[ri].sum}{$delimiter}{$report[ri].min_paid}
{/section}
