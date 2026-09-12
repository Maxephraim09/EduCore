🎓 EduCore
Financial Management System with Integrated Blog
Developed by MGTechs Smart Innovations

https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white
https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white
https://img.shields.io/badge/Bootstrap-5.1-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white
https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge

EduCore is a comprehensive, all-in-one financial management system built specifically for educational institutions. It combines powerful financial tools with a modern content management system, enabling schools to manage students, fees, employees, salaries, loans, expenses, assets, and blog content — all from a single, secure platform.

📋 Table of Contents
About the Developer

Features

System Architecture

Role-Based Access Control

Blog System

Installation

Configuration

Default Users

Database Schema

API Routes

Receipt Verification

School Settings

Support

License

👨‍💻 About the Developer
<div align="center">
MGTechs Smart Innovations
Building Smart Solutions for Modern Institutions

Developer	Maxwell Ephraim Halilu
Company	MGTechs Smart Innovations
RC Number	RC 9713678
Website	www.mgtechs.com.ng
Email	mgtechs09@gmail.com
Phone	+234 816 159 5906
</div>
✨ Features
💰 Financial Management
Module	Description
Student Management	Complete CRUD operations for student records with class assignments
Fee Payments	Collect payments, generate receipts, Paystack integration
Employee Management	Staff records, departments, positions, and salaries
Salary Processing	Monthly salary processing with approval workflow
Staff Loans	Loan applications, approvals, and repayment tracking
Staff Overdrafts	Overdraft management with withdrawal/repayment records
Expense Tracking	Create, approve, and manage institutional expenses
Asset Management	Track assets with automatic depreciation calculations
Income Management	Record and categorize other income sources
Financial Reports	Fee collection, salary, and P&L reports with exports
📝 Blog System
Feature	Description
Category Management	Organize posts into customizable categories
Post Creation	Rich text editor with tags and featured images
Approval Workflow	Teachers submit, admins/frontdesk approve
Comment System	Nested comments with moderation capabilities
View Tracking	Analytics for post performance and engagement
Tag System	Categorize content with searchable tags
Public Blog	SEO-friendly public-facing blog pages
Featured Posts	Highlight important announcements
🔐 Security & Access Control
Role-Based Access Control (RBAC) — 7 distinct roles with granular permissions

Permission Management — Database-driven permission system

Activity Logging — Complete audit trail of all user actions

Session Management — Secure authentication with remember-me

Receipt Verification — QR code-based public verification

Password Reset — Secure email-based password recovery

💳 Payment Integration
Paystack Gateway — Secure online payments for fees

Receipt Generation — PDF receipts with QR codes

Payment Verification — Public verification page for receipts

Payment History — Complete transaction records per student

🏗 System Architecture
text
┌─────────────────────────────────────────────────────────────────┐
│                        EduCore Platform                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    Presentation Layer                     │   │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ │   │
│  │  │ Admin  │ │ Public │ │ Parent │ │Student │ │Teacher │ │   │
│  │  │Dashboard│ │ Blog   │ │ Portal │ │ Portal │ │ Portal │ │   │
│  │  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    Application Layer                      │   │
│  │  ┌────────────┐ ┌──────────┐ ┌────────┐ ┌────────────┐  │   │
│  │  │Controllers │ │Middleware│ │Services│ │  Events    │  │   │
│  │  └────────────┘ └──────────┘ └────────┘ └────────────┘  │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                      Domain Layer                         │   │
│  │  ┌────────┐ ┌────────────┐ ┌────────┐ ┌──────────────┐  │   │
│  │  │ Models │ │Repositories│ │Policies│ │  Observers   │  │   │
│  │  └────────┘ └────────────┘ └────────┘ └──────────────┘  │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              │                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                  Infrastructure Layer                     │   │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ │   │
│  │  │ MySQL  │ │Paystack│ │  SMS   │ │ Email  │ │Storage │ │   │
│  │  │Database│ │Gateway │ │Gateway │ │  SMTP  │ │ Local  │ │   │
│  │  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
👥 Role-Based Access Control
Role Hierarchy
text
┌─────────────────────────────────────────────────────────────────┐
│                        SUPER ADMIN                               │
│                    (Full System Control)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────┐  ┌────────────┐  ┌──────────┐  ┌──────────────┐  │
│  │  ADMIN   │  │ ACCOUNTANT │  │  TEACHER │  │  FRONTDESK   │  │
│  │          │  │            │  │          │  │              │  │
│  │Operations│  │ Financial  │  │ Academic │  │  Student/    │  │
│  │Management│  │ Operations │  │   Staff  │  │Parent Support│  │
│  └──────────┘  └────────────┘  └──────────┘  └──────────────┘  │
│                                                                  │
│  ┌──────────┐  ┌────────────┐                                   │
│  │  PARENT  │  │  STUDENT   │                                   │
│  │          │  │            │                                   │
│  │ View Own │  │  View Own  │                                   │
│  │Children  │  │   Info     │                                   │
│  └──────────┘  └────────────┘                                   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
Role Descriptions
Role	Description	Key Permissions
Super Admin	Full system control with no restrictions	All modules, user management, settings, backups, audit logs
Admin	Day-to-day operational management	Students, fees, employees, salaries, loans, overdrafts, expenses, assets, incomes, reports
Accountant	Financial operations focus	Fee collection, salary processing, loan/overdraft recording, expense creation, financial reports
Teacher	Academic staff with limited access	View assigned students, submit blog posts, expense requests, view own salary/loan
Front Desk	Student and parent interaction focus	Student management, fee collection, blog categories, receipt printing, parent communication
Parent	View their child(ren)'s information only	Child's profile, fee history, fee structure, online payments, receipts, queries
Student	View own information only	Own profile, fee history, receipts
Permission Matrix
Permission	Super Admin	Admin	Accountant	Teacher	Frontdesk	Parent	Student
View Students	✅	✅	✅	❌	✅	❌	❌
Create Students	✅	✅	❌	❌	✅	❌	❌
Edit Students	✅	✅	❌	❌	✅	❌	❌
Delete Students	✅	✅	❌	❌	❌	❌	❌
Collect Payments	✅	✅	✅	❌	✅	✅	❌
View Fee Structure	✅	✅	✅	✅	✅	✅	❌
Edit Fee Structure	✅	✅	❌	❌	❌	❌	❌
Process Refunds	✅	✅	❌	❌	❌	❌	❌
View Employees	✅	✅	✅	❌	❌	❌	❌
Create Employees	✅	✅	❌	❌	❌	❌	❌
Process Salaries	✅	✅	✅	❌	❌	❌	❌
View Own Salary	✅	✅	✅	✅	✅	❌	❌
Approve Loans	✅	✅	❌	❌	❌	❌	❌
Approve Overdrafts	✅	✅	❌	❌	❌	❌	❌
Approve Expenses	✅	✅	❌	❌	❌	❌	❌
Create Expenses	✅	✅	✅	✅	❌	❌	❌
Submit Blog Post	✅	✅	❌	✅	✅	❌	❌
Approve Blog Post	✅	✅	❌	❌	✅	❌	❌
Manage Blog Categories	✅	✅	❌	❌	✅	❌	❌
View Financial Reports	✅	✅	✅	❌	❌	❌	❌
Manage Users	✅	❌	❌	❌	❌	❌	❌
System Settings	✅	👁	❌	❌	❌	❌	❌
Backup & Restore	✅	❌	❌	❌	❌	❌	❌
View Audit Logs	✅	❌	❌	❌	❌	❌	❌
Legend: ✅ Full Access · 👁 View Only · ❌ No Access

📝 Blog System
Blog Features Overview
text
┌─────────────────────────────────────────────────────────────────┐
│                        EduCore Blog                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │  Categories  │───▶│    Posts     │───▶│   Comments   │       │
│  │              │    │              │    │              │       │
│  │ • Academic   │    │ • Title      │    │ • Nested     │       │
│  │ • Events     │    │ • Content    │    │ • Moderated  │       │
│  │ • News       │    │ • Image      │    │ • Approved   │       │
│  │ • Sports     │    │ • Tags       │    │ • Replies    │       │
│  └──────────────┘    └──────────────┘    └──────────────┘       │
│         │                   │                   │               │
│         │                   │                   │               │
│         ▼                   ▼                   ▼               │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │  Slug/SEO    │    │    Tags      │    │  Moderation  │       │
│  │              │    │              │    │              │       │
│  │ • URL Slug   │    │ • Searchable │    │ • Approve    │       │
│  │ • Meta Title │    │ • Filterable │    │ • Reject     │       │
│  │ • Meta Desc  │    │ • Related    │    │ • Delete     │       │
│  └──────────────┘    └──────────────┘    └──────────────┘       │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                     View Tracking                         │   │
│  │  • Total Views  • Unique Visitors  • Reading Time        │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
Blog Workflow
text
 Teacher/Admin                    Admin/Frontdesk                    Public
      │                                │                              │
      │  ┌─────────────┐               │                              │
      └─▶│ Create Post │               │                              │
         │   (Draft)   │               │                              │
         └──────┬──────┘               │                              │
                │                      │                              │
                ▼                      │                              │
         ┌─────────────┐               │                              │
         │   Submit    │               │                              │
         │ for Approval│               │                              │
         └──────┬──────┘               │                              │
                │                      │                              │
                ▼                      ▼                              │
         ┌─────────────┐        ┌─────────────┐                       │
         │   Pending   │───────▶│   Review    │                       │
         │   Approval  │        │    Post     │                       │
         └─────────────┘        └──────┬──────┘                       │
                                       │                              │
                           ┌───────────┴───────────┐                  │
                           │                       │                  │
                           ▼                       ▼                  │
                    ┌─────────────┐         ┌─────────────┐          │
                    │  Approved   │         │  Rejected   │          │
                    │ (Published) │         │(With Reason)│          │
                    └──────┬──────┘         └─────────────┘          │
                           │                                         │
                           ▼                                         │
                    ┌─────────────┐         ┌─────────────┐          │
                    │ Visible on  │────────▶│ Public Blog │◀─────────┘
                    │ Public Blog │         │    Page     │
                    └─────────────┘         └──────┬──────┘
                                                   │
                                                   ▼
                                            ┌─────────────┐
                                            │  Comments   │
                                            │  & Views    │
                                            └─────────────┘
Blog Capabilities by Role
Action	Super Admin	Admin	Frontdesk	Teacher	Public
Create Category	✅	✅	✅	❌	❌
Edit Category	✅	✅	✅	❌	❌
Delete Category	✅	✅	✅	❌	❌
Create Post	✅	✅	✅	✅	❌
Edit Own Post	✅	✅	✅	✅	❌
Edit Any Post	✅	✅	✅	❌	❌
Submit for Approval	✅	✅	✅	✅	❌
Approve Post	✅	✅	✅	❌	❌
Reject Post	✅	✅	✅	❌	❌
Delete Post	✅	✅	✅	❌	❌
Manage Comments	✅	✅	✅	❌	❌
Approve Comments	✅	✅	✅	❌	❌
View Post Stats	✅	✅	✅	✅ (own)	❌
Submit Comment	✅	✅	✅	✅	✅
View Public Blog	✅	✅	✅	✅	✅
🚀 Installation
Prerequisites
PHP >= 8.1

Composer >= 2.0

MySQL >= 8.0

Node.js >= 16.x & NPM >= 8.x

Git

Step-by-Step Installation
bash
# 1. Clone the repository
git clone https://github.com/mgtechs/educore.git
cd educore

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=educore
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Seed permissions and default data
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=BlogSeeder
php artisan db:seed --class=UserSeeder

# 9. Create storage link
php artisan storage:link

# 10. Build assets
npm run build

# 11. Start the server
php artisan serve
Quick Setup Script
Create a file named setup.sh in the project root:

bash
#!/bin/bash

# EduCore Setup Script
# Developed by MGTechs Smart Innovations

echo "🎓 Setting up EduCore..."
echo "Developed by MGTechs Smart Innovations"
echo "=========================================="

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install

echo "📦 Installing NPM dependencies..."
npm install

# Environment setup
echo "⚙️  Setting up environment..."
cp .env.example .env
php artisan key:generate

# Database setup
echo "🗄️  Setting up database..."
php artisan migrate:fresh
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=BlogSeeder
php artisan db:seed --class=UserSeeder

# Storage
echo "🔗 Creating storage link..."
php artisan storage:link

# Build assets
echo "🏗️  Building assets..."
npm run build

# Cache
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ EduCore setup complete!"
echo "=========================================="
echo "Run 'php artisan serve' to start the server."
echo ""
echo "Support: mgtechs09@gmail.com"
echo "Website: www.mgtechs.com.ng"
echo "Phone: +234 816 159 5906"
Make it executable and run:

bash
chmod +x setup.sh
./setup.sh
⚙️ Configuration
Environment Variables
env
# ============================================
# EduCore Configuration
# Developed by MGTechs Smart Innovations
# ============================================

# Application
APP_NAME="EduCore"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=educore
DB_USERNAME=root
DB_PASSWORD=

# Paystack Payment Gateway
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxxxxxxxx
PAYSTACK_PAYMENT_URL=https://api.paystack.co

# SMS Gateway (Twilio/Nexmo/Termii)
SMS_PROVIDER=twilio
SMS_API_KEY=your_sms_api_key
SMS_SENDER_ID=EduCore

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@educore.com"
MAIL_FROM_NAME="${APP_NAME}"

# Blog Settings
BLOG_POSTS_PER_PAGE=12
BLOG_COMMENTS_ENABLED=true
BLOG_AUTO_APPROVE_COMMENTS=false
BLOG_REQUIRE_EMAIL_VERIFICATION=true

# School Settings
SCHOOL_NAME="Your School Name"
SCHOOL_TAGLINE="Excellence in Education"
SCHOOL_ADDRESS="123 Education Street"
SCHOOL_PHONE="+234 800 000 0000"
SCHOOL_EMAIL="info@school.com"
SCHOOL_WEBSITE="https://school.com"
SCHOOL_LOGO_URL="/uploads/logo.png"
School Settings via Admin UI
Navigate to Settings → School Settings to configure:

Setting Key	Description	Default
school_name	Display name on receipts and public pages	"Your School Name"
school_tagline	Short tagline shown beneath the name	"Excellence in Education"
school_address	Postal address shown on receipts	-
school_phone	Contact phone number	-
school_email	Contact email address	-
school_website	Optional website URL	-
school_logo_url	Absolute URL or path to logo image	/assets/logo.svg
Custom Logo Setup
bash
# Place logo in public/uploads/
cp your-logo.png public/uploads/logo.png

# Or set in database
php artisan tinker
>>> DB::table('system_settings')->updateOrInsert(
...     ['key' => 'school_logo_url'],
...     ['value' => '/uploads/logo.png']
... );
👤 Default Users
After running the UserSeeder, these users are created:

Role	Email	Password	Dashboard Redirect
Super Admin	superadmin@educore.com	password	/dashboard
Admin	admin@educore.com	password	/dashboard
Accountant	accountant@educore.com	password	/fee-payments
Teacher	teacher@educore.com	password	/students
Front Desk	frontdesk@educore.com	password	/students
Parent	parent@educore.com	password	/parent/dashboard
Student	student@educore.com	password	/student/dashboard
⚠️ Important: Change all default passwords immediately after installation!

🗄 Database Schema
Core Tables
text
users
├── id
├── name
├── email
├── password
├── role (enum: super_admin, admin, accountant, teacher, frontdesk, parent, student)
├── student_id (FK → students)
├── parent_id (FK → users)
├── is_active
├── is_verified
├── last_login_at
└── timestamps

students
├── id
├── admission_number
├── first_name
├── last_name
├── class
├── parent_name
├── parent_phone
├── parent_email
├── is_active
└── timestamps

fee_payments
├── id
├── student_id (FK → students)
├── amount
├── payment_method
├── reference
├── status (pending, success, failed)
├── paid_at
└── timestamps

employees
├── id
├── user_id (FK → users)
├── employee_number
├── position
├── department
├── salary
└── timestamps

expenses
├── id
├── title
├── amount
├── category
├── status (pending, approved, rejected)
├── created_by (FK → users)
├── approved_by (FK → users)
├── approved_at
└── timestamps

activity_logs
├── id
├── user_id (FK → users)
├── action
├── module
├── description
├── ip_address
├── user_agent
├── old_data (JSON)
├── new_data (JSON)
└── timestamps

system_settings
├── id
├── key
├── value
├── group
└── timestamps
Blog Tables
text
blog_categories
├── id
├── name
├── slug (unique)
├── description
├── is_active
└── timestamps

blog_posts
├── id
├── title
├── slug (unique)
├── excerpt
├── content
├── featured_image
├── category_id (FK → blog_categories)
├── author_id (FK → users)
├── status (draft, pending, approved, rejected)
├── rejection_reason
├── approved_by (FK → users)
├── approved_at
├── published_at
├── views_count
├── is_featured
├── meta_title
├── meta_description
└── timestamps

blog_tags
├── id
├── name
├── slug (unique)
└── timestamps

blog_post_tag
├── post_id (FK → blog_posts)
└── tag_id (FK → blog_tags)

blog_comments
├── id
├── post_id (FK → blog_posts)
├── parent_id (FK → blog_comments)
├── user_id (FK → users)
├── author_name
├── author_email
├── content
├── is_approved
└── timestamps

blog_post_views
├── id
├── post_id (FK → blog_posts)
├── ip_address
├── user_agent
└── viewed_at
🛣 API Routes
Authentication Routes
php
// Public routes
GET     /login                  → Show login form
POST    /login                  → Authenticate user
POST    /logout                 → Logout user

// Password reset
GET     /password/reset         → Show reset form
POST    /password/email         → Send reset link
GET     /password/reset/{token} → Show reset form
POST    /password/reset         → Reset password
Blog Routes (Public)
php
GET     /blog                       → Blog index (paginated)
GET     /blog/{slug}                → Single blog post
GET     /blog/category/{slug}       → Posts by category
GET     /blog/tag/{slug}            → Posts by tag
POST    /blog/{post}/comment        → Submit comment
GET     /blog/search                → Search posts
Blog Routes (Admin/Frontdesk)
php
// Categories
GET     /admin/blog/categories              → List categories
GET     /admin/blog/categories/create       → Create form
POST    /admin/blog/categories              → Store category
GET     /admin/blog/categories/{id}/edit    → Edit form
PUT     /admin/blog/categories/{id}         → Update category
DELETE  /admin/blog/categories/{id}         → Delete category

// Posts
GET     /admin/blog/posts                   → List posts
GET     /admin/blog/posts/create            → Create form
POST    /admin/blog/posts                   → Store post
GET     /admin/blog/posts/{id}/edit         → Edit form
PUT     /admin/blog/posts/{id}              → Update post
DELETE  /admin/blog/posts/{id}              → Delete post
POST    /admin/blog/posts/{id}/approve      → Approve post
POST    /admin/blog/posts/{id}/reject       → Reject post

// Comments
GET     /admin/blog/comments                → List comments
POST    /admin/blog/comments/{id}/approve   → Approve comment
DELETE  /admin/blog/comments/{id}           → Delete comment

// Tags
GET     /admin/blog/tags                    → List tags
POST    /admin/blog/tags                    → Create tag
DELETE  /admin/blog/tags/{id}               → Delete tag
Blog Routes (Teacher)
php
GET     /teacher/blog/posts                 → List own posts
GET     /teacher/blog/posts/create          → Create form
POST    /teacher/blog/posts                 → Store post (pending)
GET     /teacher/blog/posts/{id}/edit       → Edit form
PUT     /teacher/blog/posts/{id}            → Update post
DELETE  /teacher/blog/posts/{id}            → Delete draft
GET     /teacher/blog/posts/{id}/stats      → View post stats
Financial Routes
php
// Students
GET     /students                           → List students
GET     /students/create                    → Create form
POST    /students                           → Store student
GET     /students/{id}                      → Show student
GET     /students/{id}/edit                 → Edit form
PUT     /students/{id}                      → Update student
DELETE  /students/{id}                      → Delete student

// Fee Payments
GET     /fee-payments                       → List payments
GET     /fee-payments/create                → Payment form
POST    /fee-payments/initialize            → Initialize Paystack
GET     /fee-payments/callback              → Paystack callback
GET     /fee-payments/receipt/{id}          → View receipt
GET     /fee-payments/history/{studentId?}  → Payment history

// Reports
GET     /reports                            → Reports dashboard
GET     /reports/fee-collection             → Fee collection report
GET     /reports/salary                     → Salary report
GET     /reports/profit-loss                → P&L report

// Settings (Super Admin)
GET     /settings                           → Settings dashboard
POST    /settings/update-paystack           → Update Paystack keys
POST    /settings/update-sms                → Update SMS settings
POST    /settings/update-email              → Update Email settings
POST    /settings/update-school             → Update School settings
GET     /settings/backup                    → Backup database
POST    /settings/restore                   → Restore database
GET     /settings/logs                      → View activity logs
Receipt Verification Routes
php
GET     /verify/receipt/{id}                → Public verification page
GET     /verify/receipt/{id}/full           → Full receipt view
🔍 Receipt Verification
How It Works
text
┌─────────────────────────────────────────────────────────────────┐
│                     Receipt Generation                           │
│                                                                  │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │  Payment    │───▶│  Generate   │───▶│  Embed QR   │         │
│  │  Received   │    │  Receipt    │    │  Code       │         │
│  └─────────────┘    └─────────────┘    └──────┬──────┘         │
│                                                 │               │
│                                                 ▼               │
│                                          ┌─────────────┐        │
│                                          │  QR links   │        │
│                                          │  to /verify │        │
│                                          │  /receipt/  │        │
│                                          │  {id}       │        │
│                                          └─────────────┘        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Verification Page                             │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  ✅ Receipt Verified                                      │   │
│  │                                                           │   │
│  │  Student: John Doe                                        │   │
│  │  Amount: ₦50,000.00                                       │   │
│  │  Date: 2024-01-15                                         │   │
│  │  Reference: PAY_abc123xyz                                 │   │
│  │  Status: ✅ Paid                                          │   │
│  │                                                           │   │
│  │  [View Full Receipt]                                      │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
QR Code Generation
php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$verificationUrl = route('receipt.verify', $payment->id);
$qrCode = QrCode::size(200)
    ->format('svg')
    ->generate($verificationUrl);
🏫 School Settings
Settings Model
php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get($key, $default = null)
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    public static function set($key, $value, $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    public static function getSchoolSettings()
    {
        return [
            'name' => self::get('school_name', config('app.name')),
            'tagline' => self::get('school_tagline', 'Excellence in Education'),
            'address' => self::get('school_address', ''),
            'phone' => self::get('school_phone', ''),
            'email' => self::get('school_email', ''),
            'website' => self::get('school_website', ''),
            'logo_url' => self::get('school_logo_url', '/assets/logo.svg'),
        ];
    }
}
🆘 Support
<div align="center">
Need Help?
MGTechs Smart Innovations

Contact Method	Details
🌐 Website	www.mgtechs.com.ng
📧 Email	mgtechs09@gmail.com
📞 Phone	+234 816 159 5906
📄 RC Number	RC 9713678
👨‍💻 Developer	Maxwell Ephraim Halilu
</div>
Reporting Issues
If you encounter any issues or have feature requests, please contact us via:

Email: mgtechs09@gmail.com

Phone: +234 816 159 5906

Website: www.mgtechs.com.ng

📜 License
text
Copyright © 2024 MGTechs Smart Innovations

All rights reserved.

This software is proprietary and confidential. 
Unauthorized copying, distribution, or use of this software 
via any medium is strictly prohibited.

Developed by: Maxwell Ephraim Halilu
Company: MGTechs Smart Innovations
RC Number: RC 9713678
Website: www.mgtechs.com.ng
Email: mgtechs09@gmail.com
Phone: +234 816 159 5906
<div align="center">
🎓 EduCore
Empowering Educational Institutions with Smart Financial Management

Built with ❤️ by MGTechs Smart Innovations

https://img.shields.io/badge/Website-www.mgtechs.com.ng-blue?style=for-the-badge&logo=google-chrome&logoColor=white
https://img.shields.io/badge/Email-mgtechs09@gmail.com-red?style=for-the-badge&logo=gmail&logoColor=white
https://img.shields.io/badge/Phone-+234%2520816%2520159%25205906-green?style=for-the-badge&logo=whatsapp&logoColor=white

© 2024 MGTechs Smart Innovations. All rights reserved.

</div>
