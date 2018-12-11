  {$order.tracking}
  {$customer.s_firstname|default:$customer.firstname}
  {$customer.s_lastname|default:$customer.lastname}

  {$customer.s_address}
  {$customer.s_address_2}
  {$customer.s_city} {$customer.s_state_text}
  {$customer.s_zipcode} {$customer.s_country_text}

