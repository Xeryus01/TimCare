# ITSM Dashboard - Quick Reference Guide

## 🎯 Application at a Glance

A comprehensive IT Service Management system built with Laravel, featuring ticket management, asset tracking, room reservations, and role-based access control with a clean Tailwind CSS UI.

## 🔐 Quick Login

**Default Test Account:**
- Email: `test@example.com`
- Password: `password`

After login, you'll be redirected to `/dashboard` with overview statistics.

## 📑 Main Navigation

| Page | URL | Purpose |
|------|-----|---------|
| Dashboard | `/dashboard` | Overview & statistics |
| Tickets | `/tickets` | IT support requests |
| Assets | `/assets` | Equipment inventory |
| Reservations | `/reservations` | Room bookings |
| Profile | `/profile` | User settings |

## 🎫 Ticket Workflow

### Creating a Ticket
1. Click "Tickets" in navigation
2. Click "+ New Ticket" button
3. Fill in:
   - **Title**: Brief description (e.g., "Printer not working")
   - **Description**: Detailed explanation
   - **Category**: Hardware, Software, Network, etc.
   - **Priority**: Low, Medium, High, Critical
   - **Asset**: Link to affected equipment (optional)
4. Click "Create Ticket"

### Ticket Lifecycle
```
OPEN (🔴) 
  ↓
IN_PROGRESS (🟡)
  ↓
RESOLVED (🟢)
  ↓
CLOSED (⚪)
```

### Managing a Ticket
- **View Details**: Click ticket title to see full information
- **Edit**: Click "Edit" button to modify
- **Comment**: Add comments in the detail view
- **Mark as Resolved**: Change status to RESOLVED when fixed

### Ticket Status Colors
| Status | Color | Meaning |
|--------|-------|---------|
| OPEN | 🔴 Red | New/unassigned issue |
| ASSIGNED_DETECT | 🟡 Yellow | Assigned and in diagnostic phase |
| SOLVED_WITH_NOTES | 🟣 Purple | Solved with technician notes |
| SOLVED | 🟢 Green | Fully resolved |
| IN_PROGRESS | 🟡 Yellow | Being worked on |
| RESOLVED | 🟢 Green | Fixed, waiting closure |
| CLOSED | ⚪ Gray | Completed & archived |

## 💾 Asset Management

### Asset Statuses
| Status | Color | Meaning |
|--------|-------|---------|
| ACTIVE | 🟢 Green | Working properly |
| MAINTENANCE | 🟡 Yellow | Under maintenance |
| BROKEN | 🔴 Red | Not operational |

### Creating an Asset
1. Go to "Assets" → "+ New Asset"
2. Enter:
   - **Name**: Equipment name
   - **Type**: Computer, Printer, Server, etc.
   - **Location**: Where it's located
   - **Specs**: Technical specifications (JSON or key-value)
3. Save and track maintenance logs

### Asset Information
Each asset shows:
- Asset code (unique identifier)
- Type and location
- Current status
- Technical specifications
- Maintenance history

## 🏢 Reservation System

### Creating a Reservation
1. Navigate to "Reservations"
2. Book a room:
   - **Room**: Select from available rooms
   - **Purpose**: Meeting purpose (e.g., "Team sync")
   - **Start Time**: Date and time
   - **End Time**: When reservation ends
3. Status automatically set to PENDING
4. Confirm the reservation

### Reservation States
| Status | Color | Meaning |
|--------|-------|---------|
| PENDING | 🟡 Yellow | Waiting approval |
| CONFIRMED | 🟢 Green | Approved & booked |
| CANCELLED | 🔴 Red | No longer needed |

## 👤 User Profile

### Managing Your Account
- Click profile icon or "Profile" in menu
- Update:
  - Name
  - Email
  - Password
  - Delete account (permanent)

## 📊 Dashboard Statistics

The main dashboard shows:

```
┌─────────────────────────────────────────┐
│ 📦 Total Assets        │ 🟢 Active       │
│ 3                      │ 2               │
├─────────────────────────────────────────┤
│ 🎫 Total Tickets       │ 🔴 Open         │
│ 4                      │ 2               │
├─────────────────────────────────────────┤
│ 📋 Latest Tickets (Last 5)              │
│ • TKT-2026-003 Server backup...        │
│ • TKT-2026-001 Printer not working...  │
│ • TKT-2026-002 Email configuration...  │
│ • TKT-2026-004 Software license...     │
└─────────────────────────────────────────┘
```

## 🔍 Searching & Filtering

### List Pages
- Tables show all records
- Pagination: Navigate between pages (15 items per page)
- **Future Enhancement**: Search and filter functionality

## 🎨 Color System

### Priority Badges (Tickets)
- 🔴 **CRITICAL** - System down, urgent action needed
- 🟠 **HIGH** - Significant impact, address soon
- 🟡 **MEDIUM** - Standard request, schedule
- 🟢 **LOW** - Non-urgent, do when available

### Status Badges (General)
- 🔴 **RED** - Critical/Open/Action needed
- 🟠 **ORANGE** - High priority/Maintenance needed
- 🟡 **YELLOW** - Medium/Pending/In progress
- 🟢 **GREEN** - Low/Active/Resolved/Confirmed
- ⚪ **GRAY** - Closed/Other/Inactive

## 📱 Responsive Features

### On Mobile
- Single column layout
- Full-width tables
- Touch-friendly buttons
- Collapsible navigation

### On Desktop
- Multi-column dashboards
- Sticky sidebars
- Hover effects on tables
- Expanded view options

## ⚡ Common Tasks

### Task: Report an IT Issue
1. Go to Tickets
2. Click "+ New Ticket"
3. Describe the problem
4. Set appropriate priority
5. Submit

### Task: Find Equipment
1. Go to Assets
2. Look for asset code (e.g., AST-001)
3. Check location and status
4. View maintenance history if needed

### Task: Book a Meeting Room
1. Go to Reservations
2. Click to create new reservation
3. Select room and time
4. Confirm booking
5. Wait for approval status

### Task: Assign a Ticket
1. Go to ticket details
2. Click "Edit"
3. Select technician from "Assignee"
4. Save changes
5. Technician receives notification (if email configured)

### Task: Update Ticket Status
1. Open ticket
2. Click "Edit"
3. Change status to IN_PROGRESS or RESOLVED
4. Add comment explaining action
5. Save

### Task: Track Asset Maintenance
1. Go to Assets
2. Click on asset
3. View "Maintenance History"
4. Contact technician if maintenance needed
5. Update status when complete

## 🔔 Notifications (Coming Soon)

Future features will include:
- Real-time ticket updates
- Assignment notifications
- Reservation approvals
- Asset status changes
- Comment mentions

## 🛠️ Maintenance & Performance

### Database Maintenance
```bash
# Clear application cache
php artisan cache:clear

# Refresh all migrations
php artisan migrate:fresh --seed

# Tinker shell for debugging
php artisan tinker
```

### Asset Compilation
```bash
# Build for production
npm run build

# Development with hot reload
npm run dev
```

## 📞 Getting Help

### When Something Goes Wrong
1. **Can't login?** Check credentials: `test@example.com` / `password`
2. **Page won't load?** Run migrations: `php artisan migrate`
3. **Styles look broken?** Recompile assets: `npm run dev`
4. **Database error?** Check file permissions: `chmod 666 database/database.sqlite`

### Debug Mode
Check `.env` file for `APP_DEBUG=true` to see detailed error messages

## 🎓 Learning Resources

### Understanding the Stack
- **Laravel**: Backend framework (models, controllers, routes)
- **Blade**: Server-side template engine (for HTML rendering)
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Frontend build tool and dev server
- **SQLite**: Embedded database (no server needed)
- **Spatie Permissions**: Role-based access control package

### Key Files to Study
1. `routes/web.php` - URL to controller mappings
2. `app/Models/` - Data structure definitions
3. `app/Http/Controllers/` - Business logic
4. `resources/views/` - UI templates
5. `database/migrations/` - Database structure

## 💡 Tips & Tricks

### Speed Up Development
- Keep two terminals open: one for `php artisan serve`, one for `npm run dev`
- Edit Blade templates and see changes instantly
- Use `php artisan tinker` to test queries quickly

### Testing Data
Sample data is pre-seeded in the database:
- 1 test user
- 3 sample assets
- 4 sample tickets with different statuses
- 3 sample reservations

### Database Inspection
```bash
# Open SQLite with CLI
sqlite3 database/database.sqlite

# List all tables
.tables

# View users
SELECT * FROM users;

# View tickets with details
SELECT code, title, status, priority FROM tickets;
```

## 🚀 Next Steps for Customization

1. **Change application name**: Edit `config/app.php` `name` value
2. **Customize colors**: Edit `tailwind.config.js`
3. **Add new fields**: Create migration, update model and views
4. **Modify roles**: Edit `database/seeders/RoleSeeder.php`
5. **Change logo**: Replace in `resources/views/layouts/navigation.blade.php`

---

**Quick Links:**
- 🏠 Dashboard: `http://127.0.0.1:8000/dashboard`
- 🎫 Tickets: `http://127.0.0.1:8000/tickets`
- 💾 Assets: `http://127.0.0.1:8000/assets`
- 🏢 Reservations: `http://127.0.0.1:8000/reservations`

**Need to restart?**
```bash
# Terminal 1
php artisan serve

# Terminal 2 (in new terminal)
npm run dev
```

**Reset everything:**
```bash
php artisan migrate:fresh --seed
```

---

Version 1.0.0 | Last Updated: March 2026
