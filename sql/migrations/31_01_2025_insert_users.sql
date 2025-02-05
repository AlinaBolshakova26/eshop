-- Вставка пользователей
INSERT INTO up_user (name, phone, email, password, role) VALUES
('Администратор', '+79001234567', 'admin@example.com', SHA2('adminpass', 256), 'admin');