-- Remove question about previous years attendance
ALTER TABLE scholarships
  DROP COLUMN wm05,
  DROP COLUMN wm06,
  DROP COLUMN wm07,
  DROP COLUMN wm08,
  DROP COLUMN wm09,
  DROP COLUMN wm10,
  DROP COLUMN wm11,
  DROP COLUMN wm12,
  DROP COLUMN wm13,
  DROP COLUMN wm14,
  DROP COLUMN wm15;

-- Question about whether applicant got a scholarship last years
ALTER TABLE scholarships ADD COLUMN last_year_scholar TINYINT(1) DEFAULT NULL AFTER prev_scholar;
