-- Add column for scholarship organizations people apply for
ALTER TABLE scholarships ADD COLUMN scholarorgs VARCHAR(255) DEFAULT NULL AFTER separatejury;
