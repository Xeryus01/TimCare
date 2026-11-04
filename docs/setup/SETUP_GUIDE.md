# ITSM Dashboard - Setup & Usage Guide

Welcome to the Comprehensive ITSM (Information Technology Service Management) Dashboard System!

## 📋 Overview

This is a complete Laravel-based IT Service Management platform with:
- **Ticket Management System** - Track and manage IT support requests
- **Asset Inventory Management** - Monitor IT assets and equipment
- **Room Reservation System** - Book conference rooms and facilities
- **Role-Based Access Control** - Admin, Technician, and User roles
- **Real-time Notifications** - WebSocket-based updates via Pusher/Laravel Echo
- **Modern UI** - Responsive Tailwind CSS design with professional styling

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Node.js 18+
- SQLite (or MySQL/PostgreSQL)
- Composer

### Installation

1. **Clone the repository**
   ```bash
   cd haiti-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations & seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

### Running the Application

**Terminal 1 - Start Laravel Server:**
```bash
php artisan serve
```
Server will be available at: `http://127.0.0.1:8000`

**Terminal 2 - Start Vite Dev Server (optional for development):**
```bash
npm run dev
```

### Default Test Credentials
- **Email:** test@example.com
- **Password:** password

## 📊 Features

### 1. Dashboard
- Overview statistics (Total Assets, Active, Tickets, Open)
- Latest tickets list
- Quick action sidebar
- Color-coded status badges

### 2. Ticket Management
- **Create Tickets** - Submit IT support requests
- **Track Status** - OPEN, ASSIGNED_DETECT ("Assigned & Detect"), SOLVED_WITH_NOTES, SOLVED
- **Priority Levels** - CRITICAL, HIGH, MEDIUM, LOW
- **Add Comments** - Team communication with timestamps
- **Assign Tickets** - Assign to technicians
- **Link Assets** - Associate with IT assets

#### Ticket Statuses with Colors:

- 🔴 **OPEN** – new/unassigned issue
- 🟡 **ASSIGNED_DETECT** – ticket has been assigned and is being diagnosed
- 🟣 **SOLVED_WITH_NOTES** – marked solved with additional technician notes
- 🟢 **SOLVED** – issue fully resolved

#### Attachments

Files can be added to tickets or comments via the UI or API; supported types are handled by the attachments table and stored under `storage/app/attachments`.


#### Ticket Priorities:
- 🔴 **CRITICAL** - Urgent/system down
- 🟠 **HIGH** - Important impact
- 🟡 **MEDIUM** - Standard requests
- 🟢 **LOW** - Non-urgent tasks

### 3. Asset Management
- **Equipment Tracking** - Computers, Printers, Servers
- **Asset Status** - ACTIVE, MAINTENANCE, BROKEN
- **Technical Specs** - JSON-based specifications
- **Maintenance History** - Track all maintenance actions
- **Location Tracking** - Know where assets are

### 4. Reservation System
- **Room Booking** - Conference rooms, training spaces
- **Time Management** - Start/end times with duration calculation
- **Status Tracking** - PENDING, CONFIRMED, CANCELLED
- **Organizer Assignment** - Know who booked the room

### 5. Role-Based Access Control (RBAC)
Three pre-configured roles:

| Role | Permissions |
|------|------------|
| **Admin** | Full system access, user management, reports |
| **Technician** | View/assign tickets, update status & asset status |
| **User** | Create tickets, view own tickets/reservations |

## 🗂️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TicketViewController.php    # Ticket CRUD & comments
│   │   ├── AssetViewController.php     # Asset management
│   │   ├── ReservationViewController.php # Reservations
│   │   └── Api/
│   │       ├── TicketController.php    # API endpoints
│   │       ├── AssetController.php
│   │       └── DashboardController.php
│   └── Requests/
│       ├── StoreTicketRequest.php      # Validation rules
│       ├── StoreAssetRequest.php
│       └── StoreReservationRequest.php
├── Models/
│   ├── User.php                 # Authentication
│   ├── Ticket.php               # Ticket entity
│   ├── Asset.php                # Asset entity
│   ├── Reservation.php          # Reservation entity
│   ├── TicketComment.php        # Comments for tickets
│   ├── Attachment.php           # File attachments
│   └── Log.php                  # Audit trail

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php        # Main layout
│   ├── dashboard.blade.php      # Dashboard with stats
│   ├── tickets/
│   │   ├── index.blade.php      # Tickets list
│   │   ├── create.blade.php     # New ticket form
│   │   ├── edit.blade.php       # Edit ticket form
│   │   └── show.blade.php       # Ticket details
│   ├── assets/
│   │   ├── index.blade.php      # Assets list
│   │   ├── create.blade.php     # New asset form
│   │   └── show.blade.php       # Asset details
│   └── reservations/
│       ├── index.blade.php      # Reservations list
│       └── show.blade.php       # Reservation details
├── css/
│   └── app.css                  # Tailwind CSS
└── js/
    ├── app.js                   # Vue & Echo setup
    └── components/              # Vue components

database/
├── migrations/
│   ├── create_users_table
│   ├── create_tickets_table
│   ├── create_assets_table
│   ├── create_reservations_table
│   ├── create_ticket_comments_table
│   ├── create_attachments_table
│   ├── create_logs_table
│   └── create_permission_tables
└── seeders/
    ├── DatabaseSeeder.php       # Sample data
    ├── RoleSeeder.php           # Admin, Technician, User roles
    └── PermissionSeeder.php     # Fine-grained permissions

routes/
├── web.php                      # Web routes (Blade)
├── api.php                      # API routes
├── auth.php                     # Authentication routes
└── console.php                  # Console commands
```

## 🎨 UI Components & Styling

### Tailwind CSS Implementation
- Responsive grid layouts
- Hover effects and transitions
- Color-coded status badges
- Professional card designs
- Mobile-friendly navigation

### Page Layouts

**List Pages:**
- Header with title and creation button
- Table with hover effects
- Status/Priority badges with appropriate colors
- Pagination support
- View/Edit/Delete actions

**Detail Pages:**
- 2-column layout (main content + sidebar)
- Related information sidebar
- Professional styling with sections
- Edit/Delete buttons
- Comment threads (for tickets)

**Form Pages:**
- Card-based layout
- Field validation feedback
- Required field indicators
- Submit and cancel buttons
- Error message display

## 🔐 Authentication & Authorization

### Login
1. Navigate to `/login`
2. Use test credentials:
   - Email: `test@example.com`
   - Password: `password`

### User Roles
- Check `database/seeders/RoleSeeder.php` for role definitions
- Use `@can('permission-name')` in Blade templates to check permissions
- Use middleware `'can:permission-name'` in routes

### Creating New Users
```bash
php artisan tinker
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
]);
```

## 🔧 API Endpoints

### Tickets
- `GET /api/tickets` - List all tickets
- `POST /api/tickets` - Create ticket
- `GET /api/tickets/{id}` - Get ticket details
- `PATCH /api/tickets/{id}` - Update ticket
- `DELETE /api/tickets/{id}` - Delete ticket

### Assets
- `GET /api/assets` - List all assets
- `POST /api/assets` - Create asset
- `GET /api/assets/{id}` - Get asset details
- `PATCH /api/assets/{id}` - Update asset
- `DELETE /api/assets/{id}` - Delete asset

### Reservations
- `GET /api/reservations` - List all reservations
- `POST /api/reservations` - Create reservation
- `GET /api/reservations/{id}` - Get reservation details
- `PATCH /api/reservations/{id}` - Update reservation
- `DELETE /api/reservations/{id}` - Delete reservation

## 📝 Database Schema

### Users Table
```
id, name, email, password, email_verified_at, remember_token, timestamps
```

### Tickets Table
```
id, code, requester_id, assignee_id, asset_id, category, title, 
description, priority, status, timestamps
```

### Assets Table
```
id, asset_code, name, type, brand, model, serial_number, specs (JSON),
location, status, purchased_at, timestamps
```

### Reservations Table
```
id, requester_id, room_name, purpose, start_time, end_time, status, timestamps
```

### Ticket Comments Table
```
id, ticket_id, user_id, message, is_internal, timestamps
```

### Logs Table
```
id, actor_id, entity_type, entity_id, action, meta (JSON), timestamps
```

## 🚀 Advanced Features

### Status Badges & Colors
The system uses semantic color coding:
- 🔴 Red: Critical/OPEN/BROKEN
- 🟠 Orange: HIGH priority/MAINTENANCE
- 🟡 Yellow: MEDIUM priority/PENDING/IN_PROGRESS
- 🟢 Green: LOW priority/ACTIVE/RESOLVED/CONFIRMED
- ⚪ Gray: Other/CLOSED statuses

### Real-time Features (Coming Soon)
- Pusher integration for live notifications
- Laravel Echo for WebSocket support
- Configured but requires Pusher credentials

### Audit Logging
- All changes logged to `logs` table
- Track who did what and when
- Entity tracking for tickets, assets, etc.

## 📱 Responsive Design

All pages are fully responsive:
- Mobile: Single column layout
- Tablet: 2 column layout
- Desktop: Full 3+ column layout with sidebars

## 🐛 Troubleshooting

### Pages appear empty
- Ensure migrations are run: `php artisan migrate`
- Check Vite is compiling assets: `npm run dev`
- Verify Blade views have correct syntax

### 404 errors
- Run `php artisan route:list` to check routes
- Ensure you're accessing `/dashboard` (not `/` after login)
- Check route middleware authentication

### Database errors
- Verify SQLite file exists: `database/database.sqlite`
- Run migrations: `php artisan migrate --fresh --seed`
- Check database permissions

### Permission denied errors
- Ensure user has appropriate role
- Check role_has_permissions table
- View user roles: `php artisan tinker` => `User::find(1)->roles`

## 📚 Additional Resources

### Laravel Documentation
- https://laravel.com/docs
- https://laravel.com/docs/breeze

### Tailwind CSS
- https://tailwindcss.com/docs

### Spatie Permissions
- https://spatie.be/docs/laravel-permission

## 📝 License

This project is provided as-is for ITSM system demonstration and learning purposes.

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Laravel/Tailwind documentation
3. Check route and model definitions
4. Review database migrations

---

**Application Version:** 1.0.0  
**Last Updated:** March 2026  
**Framework:** Laravel 10 + Tailwind CSS
