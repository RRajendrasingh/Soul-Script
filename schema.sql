-- SoulScript MySQL Database Schema for Shared Hosting (Hostinger)

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `proposal_responses`;
DROP TABLE IF EXISTS `page_media`;
DROP TABLE IF EXISTS `reasons_list`;
DROP TABLE IF EXISTS `story_milestones`;
DROP TABLE IF EXISTS `page_content`;
DROP TABLE IF EXISTS `failed_attempts`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `templates`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Templates Table
CREATE TABLE `templates` (
  `template_id` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `tagline` VARCHAR(255) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `price_inr` INT NOT NULL,
  `preview_image_url` VARCHAR(500) DEFAULT NULL,
  `badge` VARCHAR(50) DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Templates
INSERT INTO `templates` (`template_id`, `name`, `tagline`, `description`, `price_inr`, `preview_image_url`, `badge`, `active`) VALUES
('anniversary_reveal', 'Anniversary Reveal', 'Celebrate your journey together', 'Live "together for" counter, vertical milestone timeline, photo gallery & signed love note card.', 499, 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80', 'Bestseller', 1),
('birthday_magic', 'Birthday Magic', 'Make their special day unforgettable', 'Happy birthday hero, countdown to next birthday, "reasons I love celebrating you" list & photo gallery.', 399, 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=800&q=80', 'Popular', 1),
('perfect_proposal', 'Perfect Proposal', 'A grand question they will never forget', 'Full emotional love letter centerpiece, romantic photo gallery & interactive Yes / Let\'s Talk response buttons.', 599, 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=800&q=80', 'Most Romantic', 1),
('long_distance_love', 'Long Distance Love', 'Bridge the miles with love', 'Dual cities & timezones header, live countdown to your next reunion date & shared playlist row.', 449, 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80', 'Trending', 1),


-- 2. Orders Table
CREATE TABLE `orders` (
  `order_id` VARCHAR(100) NOT NULL,
  `buyer_name` VARCHAR(100) NOT NULL,
  `buyer_phone` VARCHAR(20) NOT NULL,
  `buyer_email` VARCHAR(150) NOT NULL,
  `buyer_password_hash` VARCHAR(255) DEFAULT NULL,
  `template_id` VARCHAR(50) NOT NULL,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `razorpay_order_id` VARCHAR(100) DEFAULT NULL,
  `razorpay_payment_id` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `idx_payment_status` (`payment_status`),
  CONSTRAINT `fk_orders_template` FOREIGN KEY (`template_id`) REFERENCES `templates` (`template_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Pages Table
CREATE TABLE `pages` (
  `page_id` VARCHAR(100) NOT NULL,
  `order_id` VARCHAR(100) NOT NULL,
  `template_id` VARCHAR(50) NOT NULL,
  `url_slug` VARCHAR(100) NOT NULL,
  `edit_token` VARCHAR(64) NOT NULL,
  `status` ENUM('draft', 'live', 'expired') NOT NULL DEFAULT 'live',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `uk_url_slug` (`url_slug`),
  UNIQUE KEY `uk_edit_token` (`edit_token`),
  CONSTRAINT `fk_pages_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pages_template` FOREIGN KEY (`template_id`) REFERENCES `templates` (`template_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Page Content Table (Template-specific fields in nullable columns)
CREATE TABLE `page_content` (
  `page_id` VARCHAR(100) NOT NULL,
  `partner_name` VARCHAR(100) NOT NULL,
  `buyer_name` VARCHAR(100) NOT NULL,
  `hint_question` VARCHAR(255) NOT NULL,
  `hint_answer_hash` VARCHAR(255) NOT NULL,
  `tagline_quote` VARCHAR(255) DEFAULT NULL,
  `favorite_singers` VARCHAR(255) DEFAULT NULL,
  `bg_music_url` VARCHAR(500) DEFAULT NULL,
  `receiver_photo` LONGTEXT DEFAULT NULL,
  `letters_json` TEXT DEFAULT NULL,
  `tokens_json` TEXT DEFAULT NULL,
  `love_note_text` TEXT DEFAULT NULL,
  -- Template Specific Fields
  `relationship_start_date` DATE DEFAULT NULL,
  `partner_dob` DATE DEFAULT NULL,
  `love_letter_text` TEXT DEFAULT NULL,
  `buyer_city` VARCHAR(100) DEFAULT NULL,
  `buyer_timezone` VARCHAR(100) DEFAULT NULL,
  `partner_city` VARCHAR(100) DEFAULT NULL,
  `partner_timezone` VARCHAR(100) DEFAULT NULL,
  `reunion_date` DATETIME DEFAULT NULL,
  `playlist_url` VARCHAR(500) DEFAULT NULL,
  `song_title` VARCHAR(150) DEFAULT NULL,
  `song_artist` VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (`page_id`),
  CONSTRAINT `fk_content_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Story Milestones Table (For Anniversary template)
CREATE TABLE `story_milestones` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `page_id` VARCHAR(100) NOT NULL,
  `entry_order` INT NOT NULL DEFAULT 1,
  `milestone_date` VARCHAR(50) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_milestones_page` (`page_id`),
  CONSTRAINT `fk_milestones_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Reasons List Table (For Birthday template)
CREATE TABLE `reasons_list` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `page_id` VARCHAR(100) NOT NULL,
  `entry_order` INT NOT NULL DEFAULT 1,
  `reason_text` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reasons_page` (`page_id`),
  CONSTRAINT `fk_reasons_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Page Media Table
CREATE TABLE `page_media` (
  `media_id` VARCHAR(100) NOT NULL,
  `page_id` VARCHAR(100) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `display_order` INT NOT NULL DEFAULT 1,
  `caption` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`media_id`),
  KEY `idx_media_page` (`page_id`),
  CONSTRAINT `fk_media_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Proposal Responses Table (For Perfect Proposal template)
CREATE TABLE `proposal_responses` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `page_id` VARCHAR(100) NOT NULL,
  `response` ENUM('yes', 'lets_talk') NOT NULL,
  `partner_note` TEXT DEFAULT NULL,
  `responded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_proposal_page` (`page_id`),
  CONSTRAINT `fk_proposal_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Failed Attempts Lockout Table (Rate limiting hint guesses)
CREATE TABLE `failed_attempts` (
  `slug` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempts_count` INT NOT NULL DEFAULT 1,
  `locked_until` DATETIME DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`slug`, `ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- DEMO SEED DATA FOR IMMEDIATE TESTING ---
-- Demo Order 1 (Anniversary)
INSERT INTO `orders` (`order_id`, `buyer_name`, `buyer_phone`, `buyer_email`, `buyer_password_hash`, `template_id`, `amount_paid`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `created_at`)
VALUES ('ord_demo_anniversary_01', 'Rohan Sharma', '+91 98765 43210', 'rohan@example.com', 'd1abd689eb7697904e98cdc316dd3ee2f95cd86e2ca5b803ad86e538f5911aa5', 'anniversary_reveal', 499.00, 'paid', 'order_demo_101', 'pay_demo_101', NOW());

INSERT INTO `pages` (`page_id`, `order_id`, `template_id`, `url_slug`, `edit_token`, `status`, `created_at`, `expires_at`)
VALUES ('page_demo_01', 'ord_demo_anniversary_01', 'anniversary_reveal', 'ananya-rohan', 'token_demo_edit_01', 'live', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR));

-- Password hash for 'shimla' (SHA-256)
INSERT INTO `page_content` (`page_id`, `partner_name`, `buyer_name`, `hint_question`, `hint_answer_hash`, `tagline_quote`, `favorite_singers`, `bg_music_url`, `letters_json`, `tokens_json`, `love_note_text`, `relationship_start_date`)
VALUES ('page_demo_01', 'Ananya', 'Rohan', 'Where did we take our very first trip together in 2022?', 'd1abd689eb7697904e98cdc316dd3ee2f95cd86e2ca5b803ad86e538f5911aa5', 'Safar Khubsurat h manjil se bhi 🌹', 'Arijit Singh & KK', 'https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=acoustic-guitars-ambient-11200.mp3', '[{"id":1,"title":"The First Magical Spark","category":"A Beautiful Beginning","content":"My Dearest Ananya, I often find myself thinking back to the first moment our paths crossed. Your smile instantly made everything feel brand new."},{"id":2,"title":"Our Silent Sacred Promise","category":"A Heartfelt Oath","content":"Here is my little vow to you: I promise to stand by your side through stormy afternoon skies and calm golden mornings forever."}]', '[{"id":1,"title":"1 Free Warm Hug","description":"Redeemable anytime for a long, tight hug when you need it most.","badge":"Hug"},{"id":2,"title":"Late Night Ice Cream Date","description":"Redeemable for a midnight drive to your favorite ice cream parlor.","badge":"Treat"},{"id":3,"title":"Movie Night Choice","description":"You pick the movie, I make the popcorn and no complaints!","badge":"Movie"}]', 'Ananya, these past 3 years with you have been the most magical chapter of my life. From late night talks to endless chai dates, every second with you feels like home. Happy Anniversary my love!', '2022-08-15');

INSERT INTO `story_milestones` (`page_id`, `entry_order`, `milestone_date`, `title`, `description`) VALUES
('page_demo_01', 1, '2022-08-15', 'The Day We First Met', 'Met at the cozy corner cafe on a rainy afternoon.'),
('page_demo_01', 2, '2022-12-25', 'First Trip to Shimla', 'Watched the first snowfall holding hands under the pines.'),
('page_demo_01', 3, '2023-08-15', 'One Year Milestone', 'Celebrated 365 days of laughter and endless joy.'),
('page_demo_01', 4, '2024-02-14', 'Our First Apartment', 'Picked out matching mugs and built our tiny sanctuary.');

INSERT INTO `page_media` (`media_id`, `page_id`, `file_path`, `display_order`, `caption`) VALUES
('media_d1_1', 'page_demo_01', 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80', 1, 'Sunset in Shimla'),
('media_d1_2', 'page_demo_01', 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80', 2, 'Our Cozy Coffee Date'),
('media_d1_3', 'page_demo_01', 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80', 3, 'Forever & Always');

-- Demo Order 2 (Proposal)
INSERT INTO `orders` (`order_id`, `buyer_name`, `buyer_phone`, `buyer_email`, `template_id`, `amount_paid`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `created_at`)
VALUES ('ord_demo_proposal_02', 'Aman Patel', '+91 98989 12345', 'aman@example.com', 'perfect_proposal', 599.00, 'paid', 'order_demo_102', 'pay_demo_102', NOW());

INSERT INTO `pages` (`page_id`, `order_id`, `template_id`, `url_slug`, `edit_token`, `status`, `created_at`, `expires_at`)
VALUES ('page_demo_02', 'ord_demo_proposal_02', 'perfect_proposal', 'priya-aman', 'token_demo_edit_02', 'live', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR));

-- Password hash for 'paris' (SHA-256)
INSERT INTO `page_content` (`page_id`, `partner_name`, `buyer_name`, `hint_question`, `hint_answer_hash`, `love_note_text`, `love_letter_text`)
VALUES ('page_demo_02', 'Priya', 'Aman', 'What city is featured on our dream bucket-list wall art?', '56d5d0ac43d768315f35a0680255ed3b9580848403d14ccf19f7d94100195ce0', 'Priya, from the moment you walked into my life, everything became brighter and clearer. Will you make me the happiest person in the world and marry me?', 'Dearest Priya,\n\nI remember the exact moment I realized I wanted to spend the rest of my life with you. We were sitting on that quiet bench, laughing over nothing at all, and it hit me — you are my home. You bring warmth to my coldest days and light to my darkest nights.\n\nEvery milestone we have shared, every quiet Sunday morning, and every adventure has led us right here to this exact moment. I promise to support your dreams, dance with you in the rain, love you unconditionally, and cherish you forever.\n\nWill you take my hand and start our forever today?');

INSERT INTO `page_media` (`media_id`, `page_id`, `file_path`, `display_order`, `caption`) VALUES
('media_d2_1', 'page_demo_02', 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=800&q=80', 1, 'A Special Day'),
('media_d2_2', 'page_demo_02', 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80', 2, 'Unforgettable Memories');

INSERT INTO `proposal_responses` (`page_id`, `response`, `partner_note`, `responded_at`)
VALUES ('page_demo_02', 'yes', 'YES! A thousand times YES my love! ❤️', NOW());
