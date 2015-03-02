UPDATE iso_countries
  SET globalns = 'Global South'
  WHERE code in ( 'BA', 'BG', 'HR', 'MK', 'ME', 'MD', 'RS' );