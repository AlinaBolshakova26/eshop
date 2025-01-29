INSERT INTO up_user (name, phone, email, password, role)
VALUES ('Владимир Путин', '+79955468264', 'vvputin@russia@ru', '12345678', 'admin');

INSERT INTO up_item (name, description, desc_short, price)
VALUES ('Букет "Львиное сердце"', 'Ох, этот аромат, а эта фактура! Получатель такого букета будет часами любоваться им, проникаясь тем же мужеством и стойкостью, что и Геракл, которому подарила подобный букет богиня Флора.',
        '29 львиных зевов бордового цвета',  11999.99);

INSERT INTO up_image (path, item_id, is_main, width, height, description)
VALUES ('потом', 1, TRUE, 800, 600,
        'Букет из 29 бордовых львиных зевов');

INSERT INTO up_order (user_id, item_id, price, address, status)
VALUES (1, 1, 11999.99, 'Калининград, Невского, 14', 'Создан');

INSERT INTO up_tag (name)
VALUES ('Букеты');

INSERT INTO up_item_tag (item_id, tag_id)
VALUES (1, 1);