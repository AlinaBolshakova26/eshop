ALTER TABLE up_user ADD COLUMN avatar VARCHAR(255) NOT NULL DEFAULT 'default.jpg';

UPDATE  up_user SET up_user.avatar = 'default.jpg' WHERE up_user.id != 100;