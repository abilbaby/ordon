# ORDON - Organ Donation and Transplant Network

<p align="center">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="ORDON Logo">
</p>

<p align="center">
<strong>Organ Donation and Transplant Management System</strong><br>
A comprehensive Laravel-based platform for managing organ donation workflows, donor-recipient matching, and transplant operations.
</p>

---

## About ORDON

ORDON is a full-featured web application designed to automate and streamline the organ donation and transplantation process. The platform facilitates the matching of organ donors with recipients through an intelligent allocation engine, manages hospital operations, and provides role-based access for admins, hospitals, donors, and recipients.

### Problem Solved

- **Organ Shortage Crisis**: Thousands of patients die waiting for organ transplants due to inefficient matching systems
- **Manual Processes**: Traditional organ allocation relies heavily on manual coordination, leading to delays and errors
- **Lack of Transparency**: No real-time tracking or audit trails for allocation decisions
- **Security Concerns**: Sensitive medical data requires robust protection and verification mechanisms

---

## Features

### Core Features

- ✅ **User Authentication & Roles** - Multi-role authentication (Admin, Hospital, Donor, Recipient)
- ✅ **Automated Matching Engine** - Score-based donor-recipient matching algorithm
- ✅ **Blood Compatibility Service** - Intelligent blood type matching
- ✅ **Identity Verification** - Admin can verify user identities
- ✅ **Fraud Detection** - Flag and blacklist suspicious users
- ✅ **Real-time Notifications** - In-app notification system
- ✅ **Dashboard Analytics** - Role-specific dashboards with statistics
- ✅ **CSV Exports** - Export donors, recipients, matches, hospitals
- ✅ **PDF Certificates** - DomPDF integration for donation certificates
- ✅ **Audit Logging** - Track all major system actions
- ✅ **Status History** - Track entity status changes
- ✅ **Emergency Requests** - Urgent organ request system
- ✅ **Issue Reporting** - User can report problems

### User Roles

| Role | Description |
|------|-------------|
| **Admin** | Full system access: manage users, matches, hospitals, reports, settings |
| **Hospital** | Manage recipients, approve matches, schedule transplants |
| **Donor** | Manage profile, view matches, download certificates |
| **Recipient** | Track status, view queue position, monitor matches |

---

## Technology Stack

| Technology | Purpose |
|------------|---------|
| Laravel 13 | Full-stack framework |
| PHP 8.3+ | Server-side language |
| MySQL | Database |
| Blade | Template engine |
| Tailwind CSS | Styling |
| Alpine.js | JavaScript interactivity |
| Vite | Asset bundling |
| DomPDF | PDF generation |

---

## Installation

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js & npm
- MySQL 8.0+

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ordon
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` with your database credentials
   - Run migrations: `php artisan migrate`

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Seed demo data (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

---



## Project Structure

```
ordon/
├── app/
│   ├── DTO/                    # Data Transfer Objects
│   ├── Enums/                  # Enum definitions
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form requests
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic services
│   └── Observers/              # Eloquent observers
├── database/
│   ├── migrations/             # Database migrations
│   ├── seeders/                # Database seeders
│   └── factories/              # Model factories
├── resources/
│   └── views/                  # Blade templates
│       ├── admin/              # Admin dashboard views
│       ├── donor/              # Donor dashboard views
│       ├── hospital/           # Hospital dashboard views
│       ├── recipient/          # Recipient dashboard views
│       └── layouts/             # Layout templates
├── routes/
│   └── web.php                 # Application routes
└── config/                     # Configuration files
```

---

## Matching Algorithm

The allocation engine uses a weighted scoring system:

| Factor | Weight | Description |
|--------|--------|-------------|
| Urgency | 40% | Recipient's medical urgency level |
| Waiting Time | 30% | Days spent waiting for transplant |
| Compatibility | 20% | Blood type and organ compatibility |
| Distance | 10% | Geographic proximity between donor and recipient |

Priority Levels: **Critical** (90+), **High** (65-89), **Medium** (40-64), **Standard** (<40)

---

## API Routes

### Public Routes
- `/` - Landing page
- `/contact-us` - Contact form
- `/login` - User login
- `/register` - User registration
- `/recipient/register` - Public recipient registration (via invite)

### Protected Routes (by role)

**Admin Routes** (`/admin/*`)
- Dashboard, Donors, Recipients, Matches, Hospitals, Reports, Settings

**Hospital Routes** (`/hospital/*`)
- Dashboard, Approvals, Transplants, Planner

**Donor Routes** (`/donor/*`)
- Dashboard, Matches, Certificate

**Recipient Routes** (`/recipient/*`)
- Dashboard, Requests, Matches, Edit Profile

---

## Security Features

- Laravel Breeze authentication with hashed passwords
- Role-based middleware protection
- CSRF token validation
- Identity number masking (e.g., `****1234`)
- Audit logging for all major actions
- Soft deletes for data retention
- Invite link expiry for recipient registration

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Support

For support or inquiries:
- Email: support@ordon.org
- Phone: +880 1234 567890
- Address: Kerala, India

---

## Acknowledgments

- Built with [Laravel](https://laravel.com) framework
- UI styled with [Tailwind CSS](https://tailwindcss.com)
- Icons from [Heroicons](https://heroicons.com)