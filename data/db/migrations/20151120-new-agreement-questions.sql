-- Add columns for new fields in Application Agreement section
ALTER TABLE scholarships
  ADD COLUMN grantfortravelonly TINYINT(1) NOT NULL DEFAULT '0' AFTER agreestotravelconditions,
  ADD COLUMN agreestofriendlyspace TINYINT(1) NOT NULL DEFAULT '0' AFTER grantfortravelonly,
  ADD COLUMN infotrue TINYINT(1) NOT NULL DEFAULT '0' AFTER agreestofriendlyspace
  ;
