# SPEC.md - CoasConnect

## 1. Concept & Vision

CoasConnect is a medical education platform bridging clinical clerkship workflow with patient communication. COAS (medical students) monitor assigned patients via WhatsApp, submit clinical logs, and receive evaluations. The system enforces Indonesian healthcare data compliance (UU PDP) with encrypted storage and audit trails.

**Core Experience**: Clean, clinical UI prioritizing efficiency during hospital rounds. WhatsApp integration removes app friction for patients while maintaining communication records.

---

## 2. Design Language

### Aesthetic Direction
Clinical-professional. Think hospital management systems meets modern SaaS dashboard. Clean whites, medical blues, clear typography.

### Color Palette
```
Primary:    #2563EB (medical blue)
Secondary:  #10B981 (success/approved)
Accent:     #F59E0B (pending/warning)
Danger:     #EF4444 (emergency/reject)
Background: #F8FAFC (light gray)
Surface:    #FFFFFF (cards/modals)
Text:       #1E293B (primary)
Muted:      #64748B (secondary text)
```

### Typography
- Headings: Inter (600-700 weight)
- Body: Inter (400-500 weight)
- Monospace: JetBrains Mono (medical codes, IDs)
- Scale: 14px base, 1.25 ratio

### Spatial System
- 4px grid
- Spacing: 4, 8, 12, 16, 24, 32, 48, 64px
- Card padding: 24px
- Sidebar width: 280px (desktop), collapsible (mobile)

### Motion Philosophy
- Subtle, functional transitions (150-250ms)
- No decorative animations
- Loading states: skeleton screens
- Form feedback: inline validation, toast notifications

---

## 3. Layout & Structure

### Dashboard Layout
```
┌─────────────────────────────────────────────────────────┐
│ Header: Logo | Search | Notifications | Profile Menu    │
├────────────┬────────────────────────────────────────────┤
│            │                                            │
│  Sidebar   │           Main Content Area               │
│  - Nav     │           (role-specific views)            │
│  - Role    │                                            │
│    Badge   │                                            │
│            │                                            │
├────────────┴────────────────────────────────────────────┤
│ Footer: Version | Support | Compliance Status           │
└─────────────────────────────────────────────────────────┘
```

### Role-Specific Views
- **COAS**: Patient list → Patient detail (chat timeline + log form) → Progress tracker
- **Doctor**: Assigned COAS list → Pending reviews → Evaluation forms → Reports
- **Admin**: User management → Assignments → Curriculum builder → System config → Audit logs

### Responsive Strategy
- Mobile-first breakpoints: 640px, 768px, 1024px, 1280px
- Sidebar collapses to hamburger on mobile
- Tables → Cards on mobile
- Forms stack vertically

---

## 4. Features & Interactions

### 4.1 Authentication & Authorization

**Registration Flow**:
| Role | Required Fields | Additional |
|------|-----------------|------------|
| COAS | name, email, password, NIM, faculty, batch, specialization | Document upload (KTM) |
| Doctor | name, email, password, NIP, department, supervision_quota | Document upload (STR) |
| Patient | (Created by admin/doctor) name, NIK, whatsapp_number, initial_diagnosis | - |
| Admin | name, email, password | - |

**Session Behavior**:
- Remember-me: 14 days
- Inactivity timeout: 30 minutes (configurable)
- Max 3 failed attempts → 15 minute lockout
- Device tracking: show active sessions in profile

**RBAC Permissions**:
| Action | Admin | Doctor | COAS | Patient |
|--------|-------|--------|------|---------|
| Manage users | ✓ | - | - | - |
| Assign patients | ✓ | ✓ | - | - |
| Submit clinical logs | - | - | ✓ | - |
| Review & evaluate logs | - | ✓ | - | - |
| View assigned patients | - | - | ✓ | - |
| Send/receive WhatsApp | - | - | ✓ | - |
| Manage curriculum | ✓ | - | - | - |
| View audit logs | ✓ | - | - | - |

### 4.2 Patient-COAS Assignment

**Assignment Flow**:
1. Admin/Doctor selects patient → Choose available COAS
2. System validates: COAS quota not exceeded, specialization match, location compatible
3. On confirm: Assignment created with status `active`, timestamps recorded
4. Notification sent to COAS (in-app + optional WhatsApp)
5. Patient receives WhatsApp welcome message

**Assignment States**: `pending` → `active` → `completed` | `transferred` | `terminated`

**Reassignment**: Original assignment marked `transferred`, new assignment created, patient notified.

### 4.3 WhatsApp Integration

**Provider**: Meta Cloud API (primary), abstraction layer for swap

**Webhook Flow**:
```
Patient sends WA → Meta webhook → Laravel webhook controller
→ Verify signature → Parse message → Queue job
→ Find patient by WA number → Store message (encrypted if sensitive)
→ Detect emergency keywords → Dispatch event if flagged
→ Route to COAS dashboard
```

**Auto-reply Templates**:
| Trigger | Template |
|---------|----------|
| New patient unverified | "Selamat datang! Silakan ketik NIK atau nomor registrasi RS Anda untuk verifikasi." |
| Verified, no active COAS | "Nama Anda [X] telah diverifikasi. Saat ini belum ada COAS yang ditugaskan. Tim kami akan segera menugaskan." |
| Verified, COAS assigned | "Hai [X]! Anda terhubung dengan COAS [Y]. Kirimkan pesan kapan saja untuk konsultasi." |
| Emergency detected | "PERHATIAN: Pesan Anda mengandung kata kunci darurat. Tim medis akan segera menghubungi Anda." |
| Symptom report received | "Terima kasih. Gejala Anda telah dicatat. COAS [X] akan meninjaunya shortly." |

**Emergency Keywords** (configurable):
`darurat`, `urgent`, `minta tolong`, `tidak bisa bernapas`, `nyeri dada`, `pendarahan`, `pingsan`

### 4.4 Clinical Log Submission

**Form Fields**:
| Field | Type | Validation |
|-------|------|------------|
| activity_date | datetime | Required, not future, within assignment period |
| activity_type | enum | Required: anamnesis, physical_exam, procedure, education, consultation, other |
| description | richtext | Required, min 50 chars |
| competencies | multiselect | Required, at least 1 |
| attachments | files | Optional, max 3, pdf/jpg/png, max 5MB each |
| reflection | textarea | Optional, min 20 chars |
| patient_condition | enum | Required: stable, improving, worsening, critical |

**Submission States**: `draft` → `submitted` → `reviewed` (approved/revised/rejected)

**On Submit**:
1. Validate all fields
2. Attachments scanned (future: ClamAV), faces/NIK auto-blurred
3. Stored in private storage
4. Notification to assigned doctor
5. Log status → `submitted`

### 4.5 Doctor Evaluation

**Review Interface**:
- List: Pending logs sorted by submission date
- Detail view: Original log + attachments + COAS reflection
- Actions:
  - **Approve**: Add optional feedback, rate competencies (1-5), submit → status `reviewed`
  - **Request Revision**: Mandatory feedback explaining issues → status `revision_requested`
  - **Reject**: Mandatory reason → status `rejected` (rare, requires admin override to resubmit)

**Rating Rubric**:
| Score | Label | Description |
|-------|-------|-------------|
| 1 | Needs Improvement | Significant gaps, requires redo |
| 2 | Developing | Partial competency, needs more practice |
| 3 | Competent | Meets expectations for level |
| 4 | Proficient | Exceeds expectations |
| 5 | Exemplary | Outstanding, role model quality |

### 4.6 Curriculum & Competency Tracking

**Competency Tree Structure**:
```
Domain (e.g., "Patient Care")
└── Sub-domain (e.g., "History Taking")
    └── Competency (e.g., "Perform complete medical history")
        └── Indicators (e.g., "Identifies chief complaint", "Explores symptom characteristics")
```

**Progress Calculation**:
- Each approved log with competency tag = competency practiced
- Achievement = (approved logs tagged with competency) / (target per period)
- Targets configurable per competency (default: 3 approved logs)

**Visualization**:
- Progress bars per domain
- Color coding: red (<50%), yellow (50-80%), green (>80%)
- Timeline chart: competency achievement over time

### 4.7 Admin Functions

**User Management**:
- CRUD all users
- Bulk import via CSV (NIM/NIP, name, email, role, department)
- Deactivate without delete (soft delete)
- Reset password → temporary password + email notification

**Assignment Management**:
- View all assignments with filters (status, doctor, COAS, date range)
- Force reassign (with reason required)
- Terminate assignment (with confirmation + reason)

**Curriculum Builder**:
- CRUD competency tree
- Drag-drop reordering
- Set active/inactive status
- Configure targets per competency

**WhatsApp Templates**:
- CRUD templates
- Variable syntax: `{{variable_name}}`
- Preview with sample data
- Test send to admin number

**Audit Log Viewer**:
- Filterable by: user, action type, model, date range
- Export to CSV
- Retention: configurable (default 24 months)

---

## 5. Component Inventory

### Navigation Components

**Sidebar**
- States: expanded (280px), collapsed (64px), hidden (mobile)
- Active item: primary color background, bold text
- Hover: light primary background
- Icons: Lucide icons, 20px

**Header**
- Fixed position, 64px height
- Search: expandable on mobile
- Notifications: badge count, dropdown list
- Profile: avatar + name, dropdown menu

### Data Display

**PatientCard**
```
┌────────────────────────────────────────┐
│ 👤 A***a (MRN: RS-2024-00123)          │
│ DM Type 2, Hipertensi                  │
│ Status: 🟡 Active                      │
│ Last activity: 2 jam lalu              │
│                                        │
│ [Lihat Detail]                         │
└────────────────────────────────────────┘
```
- Hover: subtle shadow lift
- Name anonymized for display (first letter + *** + last letter)
- Status colors: green (stable), yellow (attention), red (critical)

**ClinicalLogRow**
- Columns: Date, Type, Description preview (truncated), Status badge, Actions
- Status badges: blue (draft), yellow (submitted), green (approved), orange (revision), red (rejected)
- Actions: View, Edit (if draft/revision), Delete (if draft)

**Timeline (WhatsApp Chat)**
- Bubble style: inbound (left, gray), outbound (right, blue)
- Timestamp below each bubble
- Date separators
- Emergency flag: red border on flagged messages

### Forms

**Input Fields**
- Default: gray border, white background
- Focus: primary color border, light blue shadow
- Error: red border, red text below
- Disabled: gray background, 50% opacity

**Select/Multiselect**
- Dropdown with search for long lists
- Tag display for selected items
- Clear all button

**File Upload**
- Drag-and-drop zone
- Preview thumbnails for images
- PDF icon for documents
- Progress bar during upload
- Remove button on uploaded files

**Rich Text Editor**
- Toolbar: bold, italic, underline, lists, links
- Min height: 200px
- Max height: 400px with scroll

### Feedback

**Toast Notifications**
- Position: top-right
- Types: success (green), error (red), warning (yellow), info (blue)
- Auto-dismiss: 5 seconds
- Manual dismiss: X button

**Modal Dialogs**
- Backdrop: 50% black
- Centered, max-width based on content
- Close: X button, click outside, Escape key
- Actions: right-aligned buttons

**Empty States**
- Centered illustration (simple SVG)
- Helpful message
- Action button if applicable

**Loading States**
- Skeleton screens for content areas
- Spinner for buttons and small areas
- Progress bar for multi-step processes

---

## 6. Technical Approach

### Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend                              │
│   Blade + Alpine.js + TailwindCSS                           │
│   Inertia.js for SPA-like experience (optional)             │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        Backend                               │
│   Laravel 12 / PHP 8.2+                                      │
│   Laravel Sanctum (API tokens)                               │
│   Laravel Pint (formatting)                                  │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
┌──────────────────┐ ┌──────────────┐ ┌────────────────┐
│     MySQL 8      │ │    Redis     │ │   WhatsApp     │
│  Encrypted cols  │ │  Sessions    │ │   Meta API     │
│  InnoDB          │ │  Queue       │ │   Webhooks     │
│  UTF8MB4         │ │  Cache       │ │                │
└──────────────────┘ └──────────────┘ └────────────────┘
```

### Models & Relationships

```
User (morphTo:able)
├── role: enum[admin,doctor,coas,patient]
├── doctor: HasOne DoctorProfile
├── coas: HasOne CoasProfile
└── patient: HasOne PatientProfile

Doctor
├── user: BelongsTo User
├── department: string
├── supervisionQuota: integer
└── assignments: HasMany (as doctor)

Coas
├── user: BelongsTo User
├── nim: string (encrypted)
├── faculty: string
├── batch: integer
├── specialization: string
└── assignments: HasMany (as coas)

Patient
├── user: BelongsTo User (for auth if self-registered)
├── nik: string (encrypted)
├── whatsappNumber: string (encrypted)
├── medicalRecordNumber: string (encrypted)
├── diagnosis: text (encrypted)
├── address: text (encrypted)
├── consentGranted: boolean
├── consentTimestamp: datetime
└── assignments: HasMany

Assignment
├── patient: BelongsTo Patient
├── coas: BelongsTo Coas
├── doctor: BelongsTo Doctor (supervising)
├── status: enum[pending,active,completed,transferred,terminated]
├── assignedAt: datetime
├── completedAt: datetime (nullable)
├── notes: text (nullable)
└── clinicalLogs: HasMany

ClinicalLog
├── assignment: BelongsTo Assignment
├── activityDate: datetime
├── activityType: enum
├── description: text
├── competencies: BelongsToMany Competency (with pivot: rating, feedback)
├── patientCondition: enum
├── reflection: text (nullable)
├── status: enum[draft,submitted,reviewed,revision_requested,rejected]
├── submittedAt: datetime
└── attachments: HasMany (files stored privately)

Competency
├── name: string
├── code: string (unique)
├── description: text
├── parent: BelongsTo Self (nullable)
├── level: integer (depth in tree)
├── targetCount: integer
├── isActive: boolean
└── children: HasMany Self

Evaluation
├── clinicalLog: BelongsTo ClinicalLog
├── evaluator: BelongsTo Doctor
├── status: enum[approved,revision_requested,rejected]
├── feedback: text
├── ratings: json (competency_id => score)
├── evaluatedAt: datetime
└── expiresAt: datetime (for revisions)

WhatsAppMessage
├── assignment: BelongsTo Assignment
├── direction: enum[inbound,outbound]
├── body: text (encrypted if sensitive)
├── isSensitive: boolean
├── isEmergency: boolean
├── receivedAt: datetime
├── processedAt: datetime (nullable)
└── metadata: json

AuditLog (polymorphic)
├── user: BelongsTo User (nullable for system actions)
├── auditable: MorphTo
├── action: string (created,updated,deleted,etc)
├── oldValues: json (nullable)
├── newValues: json (nullable)
├── ipAddress: string
├── userAgent: string
└── createdAt: datetime
```

### API Endpoints

**Authentication**
- `POST /api/auth/login` - Login, returns Sanctum token
- `POST /api/auth/logout` - Invalidate token
- `GET /api/auth/me` - Current user info

**Patients** (COAS sees only assigned)
- `GET /api/patients` - List (filtered by role)
- `GET /api/patients/{id}` - Detail (policy enforced)
- `POST /api/patients` - Create (admin/doctor)
- `PATCH /api/patients/{id}` - Update (admin/doctor)

**Assignments**
- `GET /api/assignments` - List
- `POST /api/assignments` - Create
- `PATCH /api/assignments/{id}` - Update status
- `DELETE /api/assignments/{id}` - Terminate

**Clinical Logs**
- `GET /api/clinical-logs` - List (filtered by role)
- `POST /api/clinical-logs` - Create (COAS)
- `PATCH /api/clinical-logs/{id}` - Update (COAS, if draft)
- `POST /api/clinical-logs/{id}/submit` - Submit for review

**Evaluations** (Doctor only)
- `GET /api/evaluations/pending` - Pending reviews
- `POST /api/evaluations` - Create evaluation
- `PATCH /api/evaluations/{id}` - Update evaluation

**WhatsApp Webhook**
- `POST /api/webhooks/whatsapp` - Receive incoming messages
- `GET /api/webhooks/whatsapp` - Webhook verification (GET from Meta)

**Reports**
- `GET /api/reports/progress/{coasId}` - COAS progress report
- `GET /api/reports/evaluations/{doctorId}` - Doctor's evaluation summary
- `GET /api/reports/export/{type}` - Export (PDF/Excel)

### Security Implementation

**Encryption**:
```php
// In Patient model
protected $encrypted = [
    'nik',
    'whatsapp_number',
    'medical_record_number',
    'diagnosis',
    'address',
];

// Using Laravel's encrypted cast
protected $casts = [
    'nik' => 'encrypted',
    'whatsapp_number' => 'encrypted',
    // ...
];
```

**Middleware Stack**:
1. `EnsureRole:role=doctor` - Role check
2. `EnsurePatientAccess` - Verify COAS has assignment to patient
3. `EncryptSensitiveData` - Encrypt request data before storage
4. `LogUserActivity` - Audit trail
5. `RateLimit:60,1` - 60 requests per minute

**Security Headers** (via HandleCors + custom):
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'
```

### File Structure (Key Directories)
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── PatientController.php
│   │   │   ├── AssignmentController.php
│   │   │   ├── ClinicalLogController.php
│   │   │   ├── EvaluationController.php
│   │   │   └── WhatsAppWebhookController.php
│   │   └── Web/
│   │       ├── DashboardController.php
│   │       ├── Coas/
│   │       ├── Doctor/
│   │       └── Admin/
│   ├── Middleware/
│   │   ├── EnsureRole.php
│   │   ├── EnsurePatientAccess.php
│   │   └── AuditRequest.php
│   └── Requests/
│       ├── StoreClinicalLogRequest.php
│       └── StoreEvaluationRequest.php
├── Models/
│   ├── User.php
│   ├── Doctor.php
│   ├── Coas.php
│   ├── Patient.php
│   ├── Assignment.php
│   ├── ClinicalLog.php
│   ├── Competency.php
│   ├── Evaluation.php
│   ├── WhatsAppMessage.php
│   └── AuditLog.php
├── Policies/
│   ├── PatientPolicy.php
│   ├── AssignmentPolicy.php
│   ├── ClinicalLogPolicy.php
│   └── EvaluationPolicy.php
└── Services/
    ├── WhatsApp/
    │   ├── WhatsAppService.php
    │   ├── Providers/
    │   │   └── MetaCloudProvider.php
    │   ├── WebhookProcessor.php
    │   └── TemplateEngine.php
    ├── Curriculum/
    │   ├── CompetencyTreeBuilder.php
    │   └── ProgressCalculator.php
    └── Compliance/
        ├── AuditLogger.php
        └── ConsentManager.php
```

### Database Migrations Order
1. `create_users_table` - base user with role
2. `create_profiles_tables` - doctor, coas, patient profiles
3. `create_competencies_table` - nested set for curriculum
4. `create_assignments_table`
5. `create_clinical_logs_table`
6. `create_competency_clinical_log_table` - pivot
7. `create_evaluations_table`
8. `create_whatsapp_messages_table`
9. `create_audit_logs_table` - polymorphic
10. `create_sessions_table` - for activity tracking
11. `create_notifications_table` - Laravel notifications

---

## 7. Testing Strategy

### Coverage Targets
| Module | Target |
|--------|--------|
| Overall | ≥75% |
| Assignment | ≥90% |
| ClinicalLog | ≥90% |
| WhatsAppWebhook | ≥90% |
| RBAC/Policies | ≥85% |

### Test Categories

**Unit Tests**
- `EncryptionServiceTest` - roundtrip encrypt/decrypt
- `CompetencyTreeBuilderTest` - tree construction
- `ProgressCalculatorTest` - calculation accuracy
- `WhatsAppTemplateEngineTest` - variable substitution

**Feature Tests**
- `AuthenticationTest` - login, logout, role redirect, lockout
- `AssignmentFlowTest` - full assignment lifecycle
- `ClinicalLogSubmissionTest` - submit → review → approval
- `WhatsAppWebhookTest` - incoming message handling
- `AuthorizationTest` - policy enforcement (403 for unauthorized)

**Browser Tests (Dusk)**
- `CoasDashboardTest` - login → patient list → view detail
- `ClinicalLogFormTest` - fill → submit → confirmation
- `DoctorReviewTest` - review → approve → COAS notification
- `MobileResponsiveTest` - 375px viewport usability

---

## 8. Deployment

### Requirements
- PHP 8.2+ with extensions: pdo_mysql, redis, zip, gd
- MySQL 8.0+ or MariaDB 10.5+
- Redis 6+
- Node.js 20+
- SSL certificate (HTTPS required for WhatsApp webhooks)
- Queue worker: Supervisor or systemd

### Environment Variables
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://coasconnect.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coasconnect
DB_USERNAME=coasconnect
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

WHATSAPP_PHONE_NUMBER_ID=123456789
WHATSAPP_WEBHOOK_URL=https://coasconnect.example.com/api/webhooks/whatsapp
WHATSAPP_ACCESS_TOKEN=EAAG...

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis

LOG_CHANNEL=daily
LOG_LEVEL=warning
```

### Production Checklist
- [ ] .env secured (git ignored)
- [ ] SSL certificate installed
- [ ] Queue worker running (supervisor)
- [ ] Scheduler configured (crontab)
- [ ] Backups configured (daily DB, weekly full)
- [ ] Monitoring set up (Sentry/Logstash)
- [ ] Health check endpoint responding
- [ ] WhatsApp webhook verified
- [ ] CDN configured for static assets
- [ ] Security headers verified

---

## 9. Future Enhancements (Post-MVP)
- AI-powered symptom analysis
- Video consultation integration
- LMS integration (API to campus systems)
- Mobile app (React Native)
- Multi-facility support
- Analytics dashboard with trends
