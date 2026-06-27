DELETE FROM users WHERE email='admin@admin.com';
INSERT INTO users (name, username, email, password, role, created_at, updated_at) VALUES ('Super Admin', 'admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', datetime('now'), datetime('now'));
