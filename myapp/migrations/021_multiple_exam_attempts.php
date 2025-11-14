<?php

class Migration_Multiple_exam_attempts extends CI_Migration {
    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';
    }

    public function up() {

/*
-------------------------------------------------------------
-- Add a setting for the maximum number of attempts
-- NULL values represent no maximum
ALTER TABLE bol_exam_active
	ADD COLUMN maximum_attempts INT DEFAULT 1;

----------------------------------------------------------
-- Change the name of bol_exam_status to better describe
-- its behavior
RENAME TABLE bol_exam_status TO bol_exam_attempt;

--------------------------------------------------------------
-- Consolidate bol_exam_finished into bol_exam_attempt

-- Add a column to bol_exam_attempt for storing the data 
-- from bol_exam_finished
ALTER TABLE bol_exam_attempt
ADD COLUMN attempt_count INT NOT NULL DEFAULT 1 AFTER activeexamid,
ADD COLUMN is_done BOOLEAN NOT NULL DEFAULT FALSE;

-- Remove the existing foreign key in order to change its behaviors
ALTER TABLE bol_exam_attempt DROP FOREIGN KEY bol_exam_status_ibfk_1;

-- Add the new foreign keys
ALTER TABLE bol_exam_attempt
ADD CONSTRAINT bol_exam_attempt_ibfk_1
FOREIGN KEY (activeexamid) REFERENCES bol_exam_active(id)
ON UPDATE CASCADE ON DELETE RESTRICT;
-- When a user is deleted there is a message that says all their data is delete as well
ALTER TABLE bol_exam_attempt
ADD CONSTRAINT bol_exam_attempt_ibfk_2
FOREIGN KEY (userid) REFERENCES bol_user(id)
ON UPDATE CASCADE ON DELETE CASCADE;


-- Set is_done to TRUE for the entries that have a matching
-- entry in bol_exam_finished
UPDATE bol_exam_attempt AS attempt
JOIN bol_exam_finished AS finished
    ON attempt.userid = finished.userid
    AND attempt.activeexamid = finished.activeexamid
SET attempt.is_done = TRUE;

-- Now that the data from bol_exam_finished is in bol_exam_status.is_done
-- we can delete bol_exam_finished
DROP TABLE bol_exam_finished;

---------------------------------------------------------------------
-- Add a unique constraint to make sure we don't have any duplicate
-- exam attempts
ALTER TABLE bol_exam_attempt
ADD CONSTRAINT uc_user_exam_instance_attempt_count
UNIQUE (userid, activeexamid, attempt_count);

----------------------------------------------------------------------
-- Migrate bol_exam_results to point to bol_exam_attempt.id instead
-- of bol_exam_active.id

-- Create a new column instead of replacing the existing FK column
ALTER TABLE bol_exam_results
ADD COLUMN attempt_id INT
AFTER activeexamid;

UPDATE bol_exam_results r
LEFT JOIN bol_exam_attempt a
	ON r.activeexamid = a.activeexamid
SET r.attempt_id = a.id
WHERE r.attempt_id IS NULL;
-- Make sure that there are no rows with bol_exam_results.exam_attempt_id that IS NULL

-- Make the new column NOT NULL, since all results must match up to 
-- an exam attempt
ALTER TABLE bol_exam_results
MODIFY COLUMN attempt_id INT NOT NULL;

-- Add FK
ALTER TABLE bol_exam_results
ADD CONSTRAINT bol_exam_results_ibfk_2
FOREIGN KEY (attempt_id)
REFERENCES bol_exam_attempt(id);

-- Drop the old FK and column
ALTER TABLE bol_exam_results
DROP FOREIGN KEY bol_exam_results_ibfk_1;

ALTER TABLE bol_exam_results
DROP COLUMN activeexamid;

-- We can also get the user from the exam_attempt
ALTER TABLE bol_exam_results
DROP COLUMN userid;
*/

   }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}