# Wildlife Conservation Monitoring System

CMPE344 - Database Management Systems Project  
Spring 2025-2026

## Live Demo
**URL:** [https://muath-wildlife.infinityfree.me/index.php](https://muath-wildlife.infinityfree.me/index.php)

**Login Credentials:**
- Username: `admin_wild`
- Password: `password123`

## Overview
A web-based database application for tracking wildlife species, habitats, sightings, health records, and threats. Supports three user roles with role-based access control.

## Tech Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5, Chart.js
- **Backend:** PHP
- **Database:** PostgreSQL (Supabase Cloud)
- **API:** Supabase REST API
- **Deployment:** InfinityFree

## Features
- Role-based authentication (Admin, Ranger, Researcher)
- CRUD operations for species management
- Wildlife sighting logging with species and habitat selection
- Health record tracking for individual animals
- Threat reporting (poaching, deforestation, pollution, etc.)
- Statistical dashboard with interactive charts
- Automated PL/SQL triggers and functions

## Setup Instructions
1. Place all `.php` files in your web server's root directory
2. Update `config.php` with your Supabase project URL and anon key
3. Run `database.sql` in your Supabase SQL Editor to create tables and sample data
4. Open `index.php` in your browser

## Project Structure
| File | Purpose |
|------|---------|
| `index.php` | Login page with authentication |
| `dashboard.php` | Admin dashboard with live statistics |
| `species.php` | Species CRUD management |
| `sightings.php` | Sightings logger for field rangers |
| `reports.php` | Statistical reports with charts |
| `logout.php` | Session termination |
| `config.php` | Supabase API connection |
| `database.sql` | Full DDL, DML, and PL/SQL blocks |
| `er_diagram.png` | Entity Relationship Diagram |
| `style.css` | Custom styling |

## Database Schema (8 Tables)
- **users** - Authentication and role management
- **species** - Animal/plant species catalog
- **habitats** - Geographic locations
- **species_habitats** - Many-to-many relationship
- **sightings** - Field observations
- **health_records** - Veterinary checkups
- **threats** - Environmental and human threats
- **audit_log** - System activity tracking

## Author
**Muath Ali** - Student ID: 22205797  
CMPE344 - Spring 2025-2026