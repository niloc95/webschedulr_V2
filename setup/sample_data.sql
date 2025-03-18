-- Sample clients
INSERT INTO `clients` (`user_id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'John Smith', 'john@example.com', '555-123-4567', NOW()),
(1, 'Jane Doe', 'jane@example.com', '555-987-6543', NOW()),
(1, 'Robert Johnson', 'robert@example.com', '555-555-5555', NOW());

-- Sample services
INSERT INTO `services` (`user_id`, `name`, `description`, `duration`, `price`, `created_at`) VALUES
(1, 'Consultation', 'Initial consultation meeting', 60, 75.00, NOW()),
(1, 'Follow-up', 'Follow-up appointment', 30, 45.00, NOW()),
(1, 'Full Service', 'Comprehensive service package', 90, 120.00, NOW());

-- Sample appointments (for the current date and future)
INSERT INTO `appointments` (`user_id`, `client_id`, `service_id`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 11 HOUR, 'confirmed', NOW()),
(1, 2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 10 HOUR + INTERVAL 30 MINUTE, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 11 HOUR, 'pending', NOW()),
(1, 3, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY) + INTERVAL 13 HOUR, DATE_ADD(CURDATE(), INTERVAL 3 DAY) + INTERVAL 14 HOUR + INTERVAL 30 MINUTE, 'confirmed', NOW());