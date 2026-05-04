CMPE344 - Database Management Systems Project Report

Wildlife Conservation Monitoring Database Management System

Student: Muath Ali Student ID: 22205797



Date: May 2026



1\. Introduction

This project implements a Wildlife Conservation Monitoring Database Management System. The system allows conservation organizations to track species populations, log wildlife sightings, record animal health data, monitor habitat threats, and generate statistical reports. The system supports three user roles: Admin (full system access), Ranger (field data entry), and Researcher (data analysis). The database contains 7 tables with proper relationships, constraints, and automated triggers.



2\. Database Design

2.1 ER Diagram

\[Insert ER diagram here — we'll draw this next]



2.2 Tables (7 total)

Table	Purpose	Key Fields

users	Authentication and role management	user\_id, username, role

species	Animal/plant species catalog	species\_id, common\_name, conservation\_status

habitats	Geographic locations	habitat\_id, name, type, coordinates

species\_habitats	Many-to-many linking table	species\_id, habitat\_id

sightings	Field observations	sighting\_id, count, health\_status

health\_records	Veterinary checkups	record\_id, weight\_kg, condition

threats	Poaching, deforestation, etc.	threat\_id, type, severity

audit\_log	System activity tracking	log\_id, action, table\_name

2.3 Constraints

Primary keys on all tables



Foreign keys with ON DELETE CASCADE / SET NULL



CHECK constraints on roles, conservation\_status, health\_status, threat types, severity (1-5)



UNIQUE constraints on username, email, scientific\_name



NOT NULL on required fields



DEFAULT values on dates (NOW())



2.4 DDL

Full DDL available in database.sql in the GitHub repository.



2.5 DML (Sample Data)

Sample data inserted: 4 users, 8 species, 7 habitats, 9 sightings, 5 health records, 5 threats.



3\. SQL Queries (7 Statistical Queries)

Query 1: Total sightings per species



sql

SELECT s.common\_name, COUNT(\*) AS total\_sightings, SUM(si.count) AS total\_animals

FROM species s JOIN sightings si ON s.species\_id = si.species\_id

GROUP BY s.common\_name ORDER BY total\_sightings DESC;

Query 2: Average health condition per species



sql

SELECT s.common\_name, COUNT(hr.record\_id) AS checks, AVG(hr.weight\_kg) AS avg\_weight

FROM species s LEFT JOIN health\_records hr ON s.species\_id = hr.species\_id

GROUP BY s.common\_name;

Query 3: Threats by severity per habitat



sql

SELECT h.name, COUNT(t.threat\_id) AS total\_threats, AVG(t.severity) AS avg\_severity

FROM habitats h JOIN threats t ON h.habitat\_id = t.habitat\_id

GROUP BY h.name ORDER BY avg\_severity DESC;

Query 4: Species with critically low population (<2000)



sql

SELECT common\_name, population\_estimate, conservation\_status

FROM species WHERE population\_estimate < 2000 ORDER BY population\_estimate ASC;

Query 5: Ranger activity report



sql

SELECT u.full\_name, COUNT(si.sighting\_id) AS sightings, COUNT(t.threat\_id) AS threats

FROM users u LEFT JOIN sightings si ON u.user\_id = si.user\_id

LEFT JOIN threats t ON u.user\_id = t.user\_id

WHERE u.role = 'ranger' GROUP BY u.full\_name;

Query 6: Conservation status distribution



sql

SELECT conservation\_status, COUNT(\*) AS species\_count

FROM species GROUP BY conservation\_status;

Query 7: Habitats with threat density



sql

SELECT h.name, COUNT(t.threat\_id) AS threats,

ROUND((COUNT(t.threat\_id)::DECIMAL / h.area\_sqkm) \* 100, 2) AS density

FROM habitats h LEFT JOIN threats t ON h.habitat\_id = t.habitat\_id

GROUP BY h.habitat\_id;

4\. PL/SQL Blocks (5)

Function + Trigger: update\_conservation\_status() — Auto-updates species status when population changes



Function + Trigger: log\_user\_action() — Logs all login attempts to audit\_log



Function: monthly\_sighting\_report() — Generates monthly report for a given species



Function: habitat\_threat\_score() — Calculates threat severity score per habitat



Function + Trigger: prevent\_species\_delete() — Blocks deletion of species with active sightings



5\. Software (GUI)

5.1 Technology Stack

Frontend: HTML5, CSS3 (Bootstrap 5), JavaScript (Chart.js)



Backend: PHP



Database: PostgreSQL (Supabase cloud)



Communication: Supabase REST API



5.2 Screens (5 total)

Screen	File	Function

Login	index.php	Authentication with role-based access

Dashboard	dashboard.php	Live statistics (species count, habitats, sightings, threats)

Species Management	species.php	Add/delete species records

Sightings Logger	sightings.php	Log field observations with species, habitat, count

Reports	reports.php	Bar charts and pie charts for sightings, status, threats

5.3 Database Connection

Connection via PHP cURL to Supabase REST API using anon public key. All queries use parameterized endpoints with proper authorization headers.



6\. Deployment

The database is deployed on Supabase (cloud PostgreSQL). The GUI runs on XAMPP local server (Apache + PHP) and connects to the cloud database via HTTPS REST API.



7\. GitHub Repository

Link: https://github.com/MuathAli72/wildlife-conservation-db



The repository contains:



All PHP source files (index.php, dashboard.php, species.php, sightings.php, reports.php, logout.php, config.php)



database.sql (full DDL, DML, PL/SQL blocks)



This report



8\. Conclusion

This project successfully implements a fully functional wildlife conservation monitoring system with 7 related tables, role-based authentication, CRUD operations, statistical reporting with charts, and automated database triggers. The system demonstrates practical application of database design principles, SQL querying, PL/SQL programming, and web-based GUI development.





