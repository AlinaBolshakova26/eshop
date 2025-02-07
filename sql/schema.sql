CREATE TABLE up_user (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         name VARCHAR(255) NOT NULL,
                         phone VARCHAR(255) UNIQUE NOT NULL,
                         email VARCHAR(255) UNIQUE NOT NULL,
                         password VARCHAR(255),
                         email VARCHAR(512),
                         role ENUM('admin', 'customer') DEFAULT 'customer',
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE up_item (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         name VARCHAR(255) NOT NULL,
                         description VARCHAR(1000),
                         desc_short VARCHAR(255),
                         price DECIMAL(10, 2) NOT NULL,
                         is_active BOOLEAN DEFAULT TRUE,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE up_image (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          path VARCHAR(255) NOT NULL,
                          item_id INT,
                          is_main BOOLEAN DEFAULT FALSE,
                          height INT DEFAULT NULL,
                          width INT DEFAULT NULL,
                          description VARCHAR(255),
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          FOREIGN KEY (item_id) REFERENCES up_item(id) ON DELETE CASCADE
);

CREATE TABLE up_order (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          user_id INT NOT NULL,
                          item_id INT NOT NULL,
                          price DECIMAL(10, 2) NOT NULL,
                          address TEXT NOT NULL,
                          status ENUM('Создан', 'В пути', 'Доставлен', 'Отменен') DEFAULT 'Создан',
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          FOREIGN KEY (user_id) REFERENCES up_user(id) ON DELETE CASCADE,
                          FOREIGN KEY (item_id) REFERENCES up_item(id) ON DELETE CASCADE
);

CREATE TABLE up_tag (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL UNIQUE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE up_item_tag (
                             item_id INT NOT NULL,
                             tag_id INT NOT NULL,
                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                             updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (item_id, tag_id),
                             FOREIGN KEY (item_id) REFERENCES up_item(id) ON DELETE CASCADE,
                             FOREIGN KEY (tag_id) REFERENCES up_tag(id) ON DELETE CASCADE
);

CREATE TABLE migrations (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            migration_name VARCHAR(255) NOT NULL UNIQUE,
                            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



