-- Alter schema for 2018 round questions

-- Drop unused columns
ALTER TABLE scholarships DROP COLUMN presentation;
ALTER TABLE scholarships DROP COLUMN presentationTopic;

-- Add new column for scholarship type
ALTER TABLE scholarships
  ADD COLUMN separatejury TINYINT(1) NOT NULL DEFAULT '0' AFTER chapteragree,
  ADD COLUMN missingKnowledge TEXT DEFAULT NULL AFTER collaboration
  ;
