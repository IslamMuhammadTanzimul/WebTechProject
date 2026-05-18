-- database/chef_schema_additions.sql
-- Run this after the main schema.sql
-- All chef-related tables are already included in schema.sql
-- This file contains any additional indexes or data relevant to chef functionality.

USE recipe_share_db;

-- Additional indexes to support chef analytics queries efficiently
CREATE INDEX IF NOT EXISTS idx_follows_chef_id ON follows(chef_id);
CREATE INDEX IF NOT EXISTS idx_follows_created_at ON follows(created_at);
CREATE INDEX IF NOT EXISTS idx_recipes_author_status ON recipes(author_id, status);
CREATE INDEX IF NOT EXISTS idx_recipes_is_chef_pick ON recipes(is_chef_pick);
CREATE INDEX IF NOT EXISTS idx_collection_recipes_collection_id ON collection_recipes(collection_id);
CREATE INDEX IF NOT EXISTS idx_chef_verification_status ON chef_verification_requests(status);
CREATE INDEX IF NOT EXISTS idx_chef_verification_user ON chef_verification_requests(user_id);

-- Seed some sample cuisines if not present
INSERT IGNORE INTO cuisines (name, description, flag_emoji) VALUES
('Italian', 'Classic Italian cuisine', '🇮🇹'),
('Chinese', 'Traditional Chinese dishes', '🇨🇳'),
('Mexican', 'Vibrant Mexican flavors', '🇲🇽'),
('Indian', 'Rich and spiced Indian food', '🇮🇳'),
('Japanese', 'Delicate Japanese cuisine', '🇯🇵'),
('French', 'Refined French cooking', '🇫🇷'),
('Thai', 'Aromatic Thai dishes', '🇹🇭'),
('American', 'Classic American comfort food', '🇺🇸'),
('Mediterranean', 'Healthy Mediterranean diet', '🫒'),
('Middle Eastern', 'Flavorful Middle Eastern food', '🥙');

-- Seed diet types if not present
INSERT IGNORE INTO diet_types (name) VALUES
('Vegan'),
('Vegetarian'),
('Gluten-Free'),
('Keto'),
('Paleo'),
('Dairy-Free'),
('Low-Carb'),
('Halal'),
('Kosher'),
('Nut-Free');
