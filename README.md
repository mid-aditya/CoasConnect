# CoasConnect

Medical Education & Patient Monitoring Platform for COAS (Co-Assistant)

## Overview

CoasConnect is a Laravel-based platform for managing clinical clerkship workflow, bridging medical students (COAS), supervising doctors, and patients through WhatsApp integration and comprehensive clinical log management.

## Features

- **Multi-role Authentication**: Admin, Coordinator, Doctor, COAS, Patient roles
- **Patient-COAS Assignment**: Assign and track patient assignments with status management
- **WhatsApp Integration**: Patient communication via WhatsApp Business API
- **Clinical Logs**: Submit, review, and evaluate clinical activities
- **Curriculum Tracking**: SKDI-based competency tree with progress tracking
- **Evaluation System**: Doctor reviews with ratings and feedback
- **Security**: Encrypted sensitive data, audit logging, RBAC

## Requirements

- PHP 8.2+
- Composer
- SQLite/MySQL/PostgreSQL
- Node.js 20+ (for frontend assets)

## Installation

```bash
# Clone the repository
git clone https://github.com/mid-aditya/CoasConnect.git
cd CoasConnect

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start the development server
php artisan serve
```

## Configuration

### WhatsApp Business API

Add these to your `.env` file:

```env
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_access_token
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_verify_token
```

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coasconnect
DB_USERNAME=root
DB_PASSWORD=
```

## Demo Users

After seeding, these accounts are available:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@coasconnect.local | password |
| Coordinator | koordinator@coasconnect.local | password |
| Doctor | doctor1@coasconnect.local | password |
| COAS | coas1@coasconnect.local | password |

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/logout` - Logout (requires auth)
- `GET /api/auth/me` - Get current user

### WhatsApp Webhook
- `GET /api/webhooks/whatsapp` - Webhook verification
- `POST /api/webhooks/whatsapp` - Incoming messages

### Assignments (requires auth)
- `GET /api/assignments` - List assignments
- `POST /api/assignments` - Create assignment
- `GET /api/assignments/{id}` - Get assignment
- `PATCH /api/assignments/{id}` - Update status

### Clinical Logs (requires auth)
- `GET /api/clinical-logs` - List logs
- `POST /api/clinical-logs` - Create log
- `POST /api/clinical-logs/{id}/submit` - Submit for review

### Patients (requires auth)
- `GET /api/patients` - List (filtered by role)

### Progress (COAS only)
- `GET /api/progress` - Get competency progress

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## Project Structure

```
app/
├── Http/Controllers/Api/     # API controllers
├── Models/                   # Eloquent models
├── Policies/                 # Authorization policies
├── Services/
│   ├── WhatsApp/            # WhatsApp integration
│   └── Curriculum/          # Progress calculation
└── Policies/                # RBAC policies

database/
├── migrations/              # Database schema
└── seeders/                # Sample data

routes/
├── web.php                  # Web routes
└── api.php                  # API routes
```

## Security Features

- Encrypted patient data (NIK, WhatsApp, medical records)
- Role-based access control (spatie/laravel-permission)
- Audit logging for all operations
- HTTP-only cookies, CSRF protection
- Rate limiting on API endpoints

## Compliance

Designed for Indonesian healthcare data compliance (UU PDP):
- Consent management
- Data anonymization for reporting
- Right to access/export/deletion support
- Audit trails for all data operations

## Contributing

1. Fork the repository
2. Create a feature branch
3. Run `composer pint` to format code
4. Run tests with `php artisan test`
5. Submit a pull request

## License

MIT License
