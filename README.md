# 💼 Mini VAT System

A simple and efficient **Laravel-based VAT Management System** that helps manage purchases, calculate VAT automatically, and organize product and supplier data.

---

## 🚀 Features

- 🔐 **Authentication** (Login, Register, Role-based Access)
- 🛒 **Purchase Management** (Add, Edit, Delete, View)
- 📦 **Product Management**
- 💰 **Automatic VAT Calculation**
- 👨‍💼 **Admin & User Roles**
- 🔍 **Search & Filter Purchases**
- 📅 **Date-wise Purchase Report**
- 🧾 **Pagination & Sorting**
- 🎨 **Clean and Responsive UI (Bootstrap 5)**

---

## 🧱 Built With

- **Laravel 11**
- **PHP 8.2
- **MySQL**
- **Bootstrap 5**
- **Font Awesome Icons**

---

## ⚙️ Installation Guide

1. **Clone the repository**
   ```bash
   git clone https://github.com/Rejaul-git/mini_vat_system.git
   cd mini_vat_system
Install dependencies

bash
Copy code
composer install
npm install && npm run dev
Setup environment

bash
Copy code
cp .env.example .env
php artisan key:generate
Configure your database in .env

makefile
Copy code
DB_DATABASE=mini_vat_system
DB_USERNAME=root
DB_PASSWORD=
Run migrations and seeders

bash
Copy code
php artisan migrate --seed
Start the development server

bash
Copy code
php artisan serve
Visit: http://localhost:8000

👥 Default Login Credentials
Role	Email	Password
Admin	admin@example.com	admin123
User	viewer@example.com	viewer123

