CREATE TABLE up_favorites (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              user_id INT NOT NULL,
                              item_id INT NOT NULL,
                              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              UNIQUE KEY uniq_user_item (user_id, item_id),
                              FOREIGN KEY (user_id) REFERENCES up_user(id) ON DELETE CASCADE,
                              FOREIGN KEY (item_id) REFERENCES up_item(id) ON DELETE CASCADE
);
