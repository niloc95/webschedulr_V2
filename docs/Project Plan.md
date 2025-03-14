# WebSchedulr Project Plan

## 1. Setup Module (Installation UI & Process)
✅ **Goal**: Guide the admin through initial setup, including database connection and basic configurations.

### 🔹 Features:
- **Installation form** to collect:
  - Admin user details (name, email, phone, password)
  - Business details (name, email, phone, address, timezone)
- Write configurations to a `.env` file.
- Initialize database tables (migrations/seeding).
- Redirect to the admin dashboard after setup.

---

## 2. Admin Dashboard
✅ **Goal**: Provide a central place to manage services, providers, and appointments.

### 🔹 Pages & Features:
- **Calendar View**
  - View, create, edit, and cancel appointments.
  - Filter by provider/service/date.
- **Provider & Service Management**
  - Add, edit, delete service providers.
  - Define working hours and breaks.
  - Assign services to providers.
- **General Settings**
  - Business details (name, logo, email, etc.).
  - Localization (timezone, date format, language).
- **Booking Rules & Logic**
  - Set appointment duration.
  - Configure cancellation/rescheduling rules.
  - Define booking limits (e.g., how far in advance users can book).

---

## 3. Booking Module (Frontend UI)
✅ **Goal**: Allow customers to book appointments seamlessly.

### 🔹 Features:
- Select provider and service.
- Choose available date and time.
- Fill in required booking details (configurable fields).
- Confirm booking and receive an email confirmation.
- Option to reschedule/cancel (if allowed).

---

## 4. Authentication & User Roles
✅ **Goal**: Secure access and manage user permissions.

### 🔹 User Types:
- **Admin** – Full control over settings, providers, and appointments.
- **Receptionist** – Can manage appointments but not change settings.
- **Provider** – Can manage their own availability and bookings.

### 🔹 Features:
- Login/logout system.
- Password reset & account recovery.
- Role-based access control (RBAC).

---

## 5. Notifications & Reminders (Future Enhancement)
✅ **Goal**: Keep customers and providers informed about appointments.

### 🔹 Features:
- Email/SMS reminders for upcoming appointments.
- Notifications for booking changes.
- Webhooks for external integrations.

---

## 6. API & Integrations (Future Enhancement)
✅ **Goal**: Enable external systems to interact with WebSchedulr.

### 🔹 Features:
- API for appointment management.
- Webhooks for real-time updates.
- Optional Google/Microsoft Calendar sync.

---

## Setup Module Breakdown
✅ **Goal**: Guide the admin through the installation process, setting up the database and initial configurations.

### 1️⃣ Database Structure (Core Tables)
We'll need the following tables to start:

#### `users` (Stores admin account details)
- `id` (Primary Key)
- `first_name`, `last_name`
- `email` (Unique)
- `phone`
- `password` (Hashed)
- `role` (Enum: admin, receptionist, provider)
- `created_at`, `updated_at`

#### `businesses` (Stores company/service provider details)
- `id` (Primary Key)
- `name` (Business Name)
- `email`
- `phone`
- `address`
- `timezone`
- `logo_path`
- `created_at`, `updated_at`

#### `settings` (Key-value storage for configurations)
- `id` (Primary Key)
- `key` (Setting name, e.g., "appointment_duration")
- `value` (Stored value)
- `created_at`, `updated_at`

### 2️⃣ Installation UI Flow
#### Step 1: Database Check
- Check if `.env` exists.
- If missing, show the setup form.

#### Step 2: Admin & Business Details Form
- Collect admin user details (name, email, phone, password).
- Collect business details (name, email, phone, address, timezone).

#### Step 3: Database & Config Setup
- Write `.env` file with DB credentials.
- Run migrations to create tables.
- Seed default admin and business settings.

#### Step 4: Redirect to Login
- Once setup is complete, redirect to the admin login page.

### 3️⃣ Laravel Implementation Plan
#### Routes:
- `GET /setup` → Show installation form
- `POST /setup` → Handle form submission and run installation

#### Controllers:
- `SetupController.php`
  - `index()` → Check if setup is needed
  - `store()` → Validate input, save data, run migrations

#### Views:
- `setup.blade.php` → Installation form

---

This structure ensures we rebuild the core first while keeping room for future features. 🚀