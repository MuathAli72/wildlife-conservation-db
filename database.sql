-- ========================================
-- WILDLIFE CONSERVATION MONITORING SYSTEM
-- DATABASE SETUP SCRIPT
-- ========================================

-- USERS TABLE
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'ranger', 'researcher')),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT NOW()
);

-- SPECIES TABLE
CREATE TABLE species (
    species_id SERIAL PRIMARY KEY,
    common_name VARCHAR(100) NOT NULL,
    scientific_name VARCHAR(150) UNIQUE NOT NULL,
    conservation_status VARCHAR(30) CHECK (conservation_status IN ('Least Concern', 'Near Threatened', 'Vulnerable', 'Endangered', 'Critically Endangered', 'Extinct in the Wild')),
    population_estimate INTEGER,
    description TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- HABITATS TABLE
CREATE TABLE habitats (
    habitat_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) CHECK (type IN ('Forest', 'Wetland', 'Grassland', 'Desert', 'Marine', 'Mountain', 'River')),
    latitude DECIMAL(9,6),
    longitude DECIMAL(9,6),
    area_sqkm DECIMAL(10,2),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT NOW()
);

-- SPECIES_HABITATS (Many-to-Many)
CREATE TABLE species_habitats (
    species_id INTEGER REFERENCES species(species_id) ON DELETE CASCADE,
    habitat_id INTEGER REFERENCES habitats(habitat_id) ON DELETE CASCADE,
    PRIMARY KEY (species_id, habitat_id)
);

-- SIGHTINGS TABLE
CREATE TABLE sightings (
    sighting_id SERIAL PRIMARY KEY,
    species_id INTEGER REFERENCES species(species_id) ON DELETE CASCADE,
    habitat_id INTEGER REFERENCES habitats(habitat_id) ON DELETE SET NULL,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    sighting_date DATE NOT NULL DEFAULT CURRENT_DATE,
    count INTEGER NOT NULL CHECK (count > 0),
    health_status VARCHAR(30) CHECK (health_status IN ('Healthy', 'Injured', 'Sick', 'Unknown')),
    notes TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- HEALTH RECORDS TABLE
CREATE TABLE health_records (
    record_id SERIAL PRIMARY KEY,
    species_id INTEGER REFERENCES species(species_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    check_date DATE NOT NULL DEFAULT CURRENT_DATE,
    weight_kg DECIMAL(7,2),
    condition VARCHAR(30) CHECK (condition IN ('Excellent', 'Good', 'Fair', 'Poor', 'Critical')),
    diagnosis TEXT,
    treatment TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- THREATS TABLE
CREATE TABLE threats (
    threat_id SERIAL PRIMARY KEY,
    habitat_id INTEGER REFERENCES habitats(habitat_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    type VARCHAR(50) CHECK (type IN ('Poaching', 'Deforestation', 'Pollution', 'Climate Change', 'Human Encroachment', 'Natural Disaster', 'Other')),
    severity INTEGER CHECK (severity BETWEEN 1 AND 5),
    description TEXT,
    reported_date DATE NOT NULL DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT NOW()
);

-- AUDIT LOG TABLE (For PL/SQL Trigger)
CREATE TABLE audit_log (
    log_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    action VARCHAR(50),
    table_name VARCHAR(50),
    record_id INTEGER,
    log_timestamp TIMESTAMP DEFAULT NOW()
);

-- SAMPLE DATA
INSERT INTO users (username, password_hash, email, role, full_name) VALUES
('admin_wild', '$2y$10$dummyhash', 'admin@wildlife.org', 'admin', 'Dr. Sarah Kimani'),
('ranger_mike', '$2y$10$dummyhash', 'mike@wildlife.org', 'ranger', 'Michael Omondi'),
('ranger_ana', '$2y$10$dummyhash', 'ana@wildlife.org', 'ranger', 'Ana Rodriguez'),
('researcher_j', '$2y$10$dummyhash', 'james@wildlife.org', 'researcher', 'Dr. James Chen');

INSERT INTO species (common_name, scientific_name, conservation_status, population_estimate, description) VALUES
('African Elephant', 'Loxodonta africana', 'Endangered', 415000, 'Largest land animal, threatened by poaching.'),
('Bengal Tiger', 'Panthera tigris tigris', 'Endangered', 2500, 'Iconic big cat of the Indian subcontinent.'),
('Mountain Gorilla', 'Gorilla beringei beringei', 'Endangered', 1063, 'Found in volcanic mountains of central Africa.'),
('Blue Whale', 'Balaenoptera musculus', 'Endangered', 15000, 'Largest animal ever known to exist.'),
('Sea Turtle', 'Chelonia mydas', 'Endangered', 85000, 'Marine turtle threatened by plastic pollution.'),
('Giant Panda', 'Ailuropoda melanoleuca', 'Vulnerable', 1864, 'Conservation success story native to China.'),
('Snow Leopard', 'Panthera uncia', 'Vulnerable', 4500, 'Elusive cat of Central Asian mountains.'),
('Bald Eagle', 'Haliaeetus leucocephalus', 'Least Concern', 250000, 'Recovered from endangered status in North America.');

INSERT INTO habitats (name, type, latitude, longitude, area_sqkm, country) VALUES
('Serengeti National Park', 'Grassland', -2.3333, 34.8333, 14750, 'Tanzania'),
('Sundarbans Mangrove', 'Forest', 21.9497, 89.1833, 10000, 'Bangladesh'),
('Virunga Mountains', 'Mountain', -1.4333, 29.5333, 8000, 'Rwanda'),
('Great Barrier Reef', 'Marine', -18.2871, 147.6992, 344400, 'Australia'),
('Sichuan Bamboo Forests', 'Forest', 30.5000, 102.5000, 15000, 'China'),
('Altai Mountains', 'Mountain', 49.0000, 89.0000, 16000, 'Mongolia'),
('Yellowstone National Park', 'Forest', 44.4279, -110.5884, 8983, 'USA');

INSERT INTO species_habitats (species_id, habitat_id) VALUES
(1,1),(1,3),(2,2),(3,3),(4,4),(5,4),(6,5),(7,6),(8,7);

INSERT INTO sightings (species_id, habitat_id, user_id, sighting_date, count, health_status, notes) VALUES
(1,1,2,'2026-03-15',12,'Healthy','Herd near water source'),
(2,2,3,'2026-03-20',1,'Injured','Trap injury on left paw'),
(3,3,2,'2026-02-10',5,'Healthy','Family group with 2 infants'),
(4,4,3,'2026-04-05',3,'Healthy','Migrating south'),
(5,4,2,'2026-03-28',15,'Sick','Plastic entanglement signs'),
(6,5,3,'2026-01-15',4,'Healthy','Feeding on bamboo'),
(8,7,3,'2026-04-10',7,'Healthy','Nesting pair with juveniles');

INSERT INTO health_records (species_id, user_id, check_date, weight_kg, condition, diagnosis, treatment) VALUES
(1,4,'2026-03-16',5200,'Good','Minor dehydration','Provided water access'),
(2,4,'2026-03-21',220,'Fair','Laceration on paw','Antibiotics administered'),
(3,4,'2026-02-11',160,'Excellent','Healthy infant','Routine checkup'),
(5,4,'2026-03-29',120,'Poor','Plastic ingestion','Surgery to remove blockage');

INSERT INTO threats (habitat_id, user_id, type, severity, description, reported_date) VALUES
(1,2,'Poaching',5,'Elephant carcass found with tusks removed','2026-02-28'),
(2,3,'Deforestation',4,'Illegal logging near tiger territory','2026-03-10'),
(3,2,'Human Encroachment',3,'New farming settlement near gorilla habitat','2026-01-20'),
(4,3,'Pollution',5,'Oil spill affecting turtle nesting beaches','2026-04-02');

-- PL/SQL BLOCKS
CREATE OR REPLACE FUNCTION update_conservation_status()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.population_estimate < 1000 THEN NEW.conservation_status := 'Critically Endangered';
    ELSIF NEW.population_estimate < 2500 THEN NEW.conservation_status := 'Endangered';
    ELSIF NEW.population_estimate < 10000 THEN NEW.conservation_status := 'Vulnerable';
    ELSIF NEW.population_estimate < 50000 THEN NEW.conservation_status := 'Near Threatened';
    ELSE NEW.conservation_status := 'Least Concern';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_status
BEFORE INSERT OR UPDATE OF population_estimate ON species
FOR EACH ROW EXECUTE FUNCTION update_conservation_status();

CREATE OR REPLACE FUNCTION log_user_action()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit_log (user_id, action, table_name, record_id)
    VALUES (NEW.user_id, TG_OP, TG_TABLE_NAME, NEW.user_id);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_log_login
AFTER INSERT ON users
FOR EACH ROW EXECUTE FUNCTION log_user_action();

CREATE OR REPLACE FUNCTION monthly_sighting_report(p_species_id INTEGER, p_year INTEGER, p_month INTEGER)
RETURNS TABLE(sighting_count BIGINT, total_animals BIGINT) AS $$
BEGIN
    RETURN QUERY SELECT COUNT(*)::BIGINT, SUM(s.count)::BIGINT
    FROM sightings s
    WHERE s.species_id = p_species_id
    AND EXTRACT(YEAR FROM s.sighting_date) = p_year
    AND EXTRACT(MONTH FROM s.sighting_date) = p_month;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION habitat_threat_score(p_habitat_id INTEGER)
RETURNS INTEGER AS $$
DECLARE
    avg_sev INTEGER; cnt INTEGER; score INTEGER;
BEGIN
    SELECT AVG(severity)::INTEGER, COUNT(*) INTO avg_sev, cnt
    FROM threats WHERE habitat_id = p_habitat_id;
    score := COALESCE(avg_sev,0) * COALESCE(cnt,0);
    RETURN score;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION prevent_species_delete()
RETURNS TRIGGER AS $$
DECLARE
    sc INTEGER;
BEGIN
    SELECT COUNT(*) INTO sc FROM sightings WHERE species_id = OLD.species_id;
    IF sc > 0 THEN
        RAISE EXCEPTION 'Cannot delete species with active sightings.';
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_prevent_species_delete
BEFORE DELETE ON species
FOR EACH ROW EXECUTE FUNCTION prevent_species_delete();