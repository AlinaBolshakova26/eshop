-- Вставка пользователей
INSERT INTO up_user (name, phone, email, password, role) VALUES
                                                             ('Анна Петрова', '+79123456789', 'anna@example.com', SHA2('-', 256), 'customer'),
                                                             ('Иван Иванов', '+79876543210', 'ivan@example.com', SHA2('-', 256), 'customer'),
                                                             ('Администратор', '+79001234567', 'admin@example.com', SHA2('adminpass', 256), 'admin');