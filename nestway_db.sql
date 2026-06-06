-- ============================================================
--  NestWay – Student Accommodation Website
--  DATABASE SCHEMA  (Phase 2)
--  For Live Hosting – import this file directly into phpMyAdmin
--  (Make sure you click your database first before importing)
-- ============================================================

-- TABLE 1: users
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    phone      VARCHAR(15),
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 2: properties
CREATE TABLE IF NOT EXISTS properties (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    city        VARCHAR(100)  NOT NULL,
    address     TEXT          NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    gender      ENUM('boys','girls','co-ed') NOT NULL,
    rating      DECIMAL(3,1)  DEFAULT 0.0,
    description TEXT,
    image_url   VARCHAR(300),
    owner_name  VARCHAR(100),
    owner_phone VARCHAR(15),
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 3: amenities
CREATE TABLE IF NOT EXISTS amenities (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- TABLE 4: property_amenities
CREATE TABLE IF NOT EXISTS property_amenities (
    property_id INT NOT NULL,
    amenity_id  INT NOT NULL,
    PRIMARY KEY (property_id, amenity_id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id)  REFERENCES amenities(id)  ON DELETE CASCADE
);

-- TABLE 5: interested_users
CREATE TABLE IF NOT EXISTS interested_users (
    user_id     INT NOT NULL,
    property_id INT NOT NULL,
    marked_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, property_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE DATA – Amenities
-- ============================================================
INSERT INTO amenities (name) VALUES
('WiFi'),
('3 Meals/Day'),
('Air Conditioning'),
('TV Lounge'),
('Power Backup'),
('24x7 Security'),
('Hot Water'),
('Cycle Stand'),
('Study Room'),
('Laundry'),
('CCTV'),
('Gym'),
('Parking'),
('Warden Support');

-- ============================================================
-- SAMPLE DATA – Properties
-- ============================================================
INSERT INTO properties (name, city, address, price, gender, rating, description, image_url, owner_name, owner_phone) VALUES
(
  'Sunrise Boys PG', 'Bhubaneswar',
  'Plot 42, Patia, Bhubaneswar, Odisha - 751024',
  7500.00, 'boys', 4.3,
  'Well-maintained PG near KIIT University with all modern facilities.',
  'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=600',
  'Mr. Ramesh Panda', '9876543210'
),
(
  'Blossom Girls PG', 'Pune',
  'Flat 5, Kothrud, Pune, Maharashtra - 411038',
  9000.00, 'girls', 4.6,
  'Safe and comfortable girls PG in Kothrud with home-like food.',
  'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=600',
  'Mrs. Sunita Joshi', '9123456789'
),
(
  'Urban Nest Co-Ed PG', 'Bangalore',
  'No. 15, 3rd Cross, Koramangala, Bangalore - 560034',
  11000.00, 'co-ed', 4.8,
  'Premium co-ed PG in Koramangala with gym and AC rooms.',
  'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600',
  'Mr. Arjun Nair', '9988776655'
),
(
  'Capital Boys PG', 'Delhi',
  'H-12, Laxmi Nagar, New Delhi - 110092',
  6500.00, 'boys', 4.1,
  'Affordable boys PG near metro station with meals included.',
  'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600',
  'Mr. Deepak Gupta', '9012345678'
),
(
  'Seaside Girls PG', 'Mumbai',
  'B-7, Andheri West, Mumbai, Maharashtra - 400058',
  13000.00, 'girls', 4.7,
  'Premium girls PG in Andheri with AC, security and daily meals.',
  'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600',
  'Mrs. Priya Shah', '9765432101'
),
(
  'Green Valley PG', 'Bhubaneswar',
  'Near Fire Station, Nayapalli, Bhubaneswar - 751012',
  5500.00, 'co-ed', 4.0,
  'Budget-friendly co-ed PG in Nayapalli with meals and WiFi.',
  'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600',
  'Mr. Bikash Das', '9654321098'
);

-- ============================================================
-- SAMPLE DATA – Property Amenities
-- ============================================================
INSERT INTO property_amenities VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,9),
(2,1),(2,2),(2,6),(2,7),(2,11),(2,14),
(3,1),(3,3),(3,5),(3,6),(3,12),(3,13),(3,11),(3,10),
(4,1),(4,2),(4,5),(4,7),(4,14),
(5,1),(5,2),(5,3),(5,6),(5,11),(5,10),
(6,1),(6,2),(6,8),(6,7);

-- ============================================================
-- SAMPLE DATA – Test User
-- ============================================================
INSERT INTO users (name, email, password, phone) VALUES
('Test Student', 'test@nestway.com', '$2y$10$examplehashedpassword123456789', '9000000001');