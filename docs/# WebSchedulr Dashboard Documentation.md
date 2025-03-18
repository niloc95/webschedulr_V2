# WebSchedulr Dashboard Documentation

## Overview

The WebSchedulr Dashboard provides a centralized view of appointment statistics, upcoming appointments, 
a mini-calendar, and recent activity. It serves as the main landing page after login and provides 
quick access to key application features.

## Features

### Statistics Cards

The dashboard displays four key statistics:
- **Total Appointments**: Count of all appointments in the system
- **Upcoming Appointments**: Count of future appointments that aren't cancelled
- **Total Clients**: Number of clients in the database
- **Total Services**: Number of services offered

### Upcoming Appointments Table

Displays a list of upcoming appointments for the next 7 days with:
- Date and time
- Client name
- Service type
- Status (confirmed, pending, cancelled, completed)
- Action buttons for editing and deleting appointments

### Weekly Distribution Chart

A bar chart showing the distribution of appointments by day of the week, helping identify busy days.

### Mini Calendar

A compact monthly calendar view with:
- Current month display
- Highlighting of the current day
- Links to day view for each calendar day

### Recent Activity Feed

Shows the latest appointment activities with:
- Client name
- Service type
- Date and time
- Creation date

## Technical Implementation

### Controller: DashboardController.php

The `DashboardController` provides:
- Database connectivity with error handling
- Safe session management
- Methods to retrieve all required dashboard data
- Fallback default values when data can't be retrieved

### View: dashboard/index.php

The dashboard view:
- Displays all statistics, calendar, and activity data
- Implements responsive design for various screen sizes
- Includes proper error handling and null checks for all variables
- Integrates Chart.js for appointment distribution visualization

### Data Flow

1. User accesses the dashboard route
2. Router forwards to the DashboardController
3. Controller authenticates user session
4. Controller retrieves data from database
5. View renders data with appropriate formatting

### Database Queries

The dashboard retrieves data using these key queries:
- Count of appointments by user
- Upcoming appointments in date range
- Client and service counts
- Appointment distribution by day of week
- Recent activities based on creation date

## Setup and Configuration

1. Ensure database tables are created using `setup/dashboard_tables.sql`
2. Add sample data using `setup/sample_data.sql` if needed
3. Configure database connection in the main `config.php`
4. Update router to include dashboard routes

## Troubleshooting

Common issues:
- Undefined variables: Ensure controller is properly initializing all variables
- Database connection failures: Check credentials in config.php
- Empty data: Verify that sample data has been added and user_id matches