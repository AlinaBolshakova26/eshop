CREATE INDEX idx_up_user_role ON up_user(role);
CREATE INDEX idx_up_user_created_at ON up_user(created_at);

CREATE INDEX idx_up_item_name ON up_item(name);
CREATE INDEX idx_up_item_is_active ON up_item(is_active);
CREATE INDEX idx_up_item_price ON up_item(price);

CREATE INDEX idx_up_image_item_id ON up_image(item_id);
CREATE INDEX idx_up_image_is_main ON up_image(is_main);

CREATE INDEX idx_up_order_user_id ON up_order(user_id);
CREATE INDEX idx_up_order_item_id ON up_order(item_id);
CREATE INDEX idx_up_order_status ON up_order(status);
CREATE INDEX idx_up_order_created_at ON up_order(created_at);

CREATE INDEX idx_up_item_tag_tag_id ON up_item_tag(tag_id);