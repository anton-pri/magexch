{section name=ri loop=$report}
{$report[ri].doc_id}{$delimiter}{$report[ri].display_id}{$delimiter}{$report[ri].customer_id}{$delimiter}{$report[ri].firstname}{$delimiter}{$report[ri].lastname}{$delimiter}{$report[ri].b_address}{$delimiter}{$report[ri].b_address_2}{$delimiter}{$report[ri].b_city}{$delimiter}{$report[ri].b_state}{$delimiter}{$report[ri].b_country}{$delimiter}{$report[ri].subtotal}{$delimiter}{$report[ri].commissions}{$delimiter}{$report[ri].paid}
{/section}
