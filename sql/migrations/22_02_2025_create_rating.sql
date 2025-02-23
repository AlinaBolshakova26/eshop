CREATE TABLE up_ratings (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         product_id INT NOT NULL,
                         user_id INT NOT NULL,
                         rating INT NOT NULL CHECK (rating >= 0 AND rating <= 5),
                         comment TEXT,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (product_id) REFERENCES up_item(id) ON DELETE CASCADE,
                         FOREIGN KEY (user_id) REFERENCES up_user(id) ON DELETE CASCADE,
                         UNIQUE KEY unique_rating (user_id, product_id)
);
