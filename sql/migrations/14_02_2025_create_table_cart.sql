CREATE TABLE up_cart (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         user_id INT NOT NULL,
                         item_id INT NOT NULL,
                         quantity INT NOT NULL DEFAULT 1,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (user_id) REFERENCES up_user(id) ON DELETE CASCADE,
                         FOREIGN KEY (item_id) REFERENCES up_item(id) ON DELETE CASCADE
)