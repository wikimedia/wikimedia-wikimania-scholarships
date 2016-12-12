ALTER TABLE scholarships
  DROP COLUMN goals;

ALTER TABLE scholarships ADD COLUMN reports TEXT AFTER last_year_scholar;
