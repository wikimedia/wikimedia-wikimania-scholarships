-- Alter schema for 2016 questions

-- Add new column for attending in 2015 (y/n)
ALTER TABLE scholarships
  ADD COLUMN wm15 TINYINT(1) DEFAULT NULL
  AFTER wm14;


-- Drop unused columns
ALTER TABLE scholarships DROP COLUMN howheard;

-- Add new column for scholarship type
ALTER TABLE scholarships
  ADD COLUMN type ENUM('partial', 'full', 'either') DEFAULT NULL
  AFTER id;
