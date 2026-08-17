USE supermarket_db;

-- Default admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password, role, status) VALUES
('Admin', 'System', 'admin@supermarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('store_name', 'SuperMarket', 'string', 'Store display name'),
('store_phone', '809-555-0001', 'string', 'Store phone number'),
('store_email', 'info@supermarket.com', 'string', 'Store email'),
('store_address', 'Santo Domingo, República Dominicana', 'string', 'Store address'),
('currency', 'RD$', 'string', 'Currency symbol'),
('tax_rate', '18.00', 'number', 'Default tax rate (ITBIS)'),
('low_stock_threshold', '10', 'number', 'Low stock alert threshold'),
('allow_guest_checkout', 'true', 'boolean', 'Allow guest checkout'),
('stripe_enabled', 'false', 'boolean', 'Enable Stripe payments');

-- Categories
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Bebidas', 'bebidas', 'Jugos, refrescos, agua y bebidas alcohólicas', 1),
('Lácteos', 'lacteos', 'Leche, queso, yogurt y derivados', 2),
('Carnes', 'carnes', 'Res, cerdo, pollo y embutidos', 3),
('Frutas y Verduras', 'frutas-y-verduras', 'Frutas frescas y vegetales', 4),
('Panadería', 'panaderia', 'Pan, repostería y productos horneados', 5),
('Limpieza', 'limpieza', 'Productos de limpieza del hogar', 6),
('Cuidado Personal', 'cuidado-personal', 'Higiene y cuidado personal', 7),
('Snacks', 'snacks', 'Galletas, chips y dulces', 8),
('Congelados', 'congelados', 'Productos congelados y helados', 9),
('Abarrotes', 'abarrotes', 'Arroz, pasta, enlatados y granos', 10);
