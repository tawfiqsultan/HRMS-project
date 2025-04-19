HRMS API (v1)

A Human Resource Management System (HRMS) RESTful API built with Laravel 12, providing core functionality for managing employees, departments, attendance, and payroll. This version uses Laravel Sanctum for secure authentication and role-based access control.

✨ Features

Authentication via Laravel Sanctum

Role-based Access: Admin & Employee

Employee Management

Department Management

Attendance Tracking (Check-in / Check-out)

Payroll Calculation & Management

Profile Editing

Forgot Password via Email with Verification Code

🔐 Roles

Admin

Full access to all modules

Add/Edit/Delete Employees

Manage Departments

View/Update Attendance

Process Payroll

Employee

View/Edit personal data

Start workday, Check-in / Check-out

View their own payroll & attendance records

🌐 Tech Stack

Laravel 12 (API-only)

MySQL as the primary database

Sanctum for API token authentication

Gmail SMTP for sending password reset verification codes

🚪 Security

Token-based authentication using Sanctum

Password reset via time-limited email verification codes

Middleware to restrict access based on user roles

🌐 API Structure

All routes are organized in the routes/api/ directory, modularized by resource (e.g., attendance, employee, department, etc.) and automatically loaded in routes/api.php.

⚙️ Installation

git clone https://github.com/your-username/hrms-api-v1.git
cd hrms-api-v1
composer install
cp .env.example .env
php artisan key:generate
# Set up your DB and Gmail SMTP in .env
php artisan migrate
php artisan serve

🚩 Disclaimer

This is the v1 of the HRMS API, built as a monolithic REST API to establish a foundation. The v2 is under development with improved architecture, performance, and scalability.

✉️ Contact

For issues or suggestions, feel free to open an issue or reach out.

Made with ❤️ using Laravel.
