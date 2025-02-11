CREATE TEMPORARY TABLE temp_images AS
SELECT * FROM up_image WHERE item_id IN (1, 5);

UPDATE up_image
SET item_id = CASE
                  WHEN item_id = 1 THEN 5
                  WHEN item_id = 5 THEN 1
    END
WHERE item_id IN (1, 5);

DROP TEMPORARY TABLE temp_images;
