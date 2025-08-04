# 🎫 ระบบจัดการปัญหา (Helpdesk System)

ระบบจัดการปัญหาที่พัฒนาด้วย Laravel และ Tailwind CSS สำหรับการจัดการปัญหาและการแจ้งเตือน

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1.svg)

## 📋 สารบัญ

- [คุณสมบัติหลัก](#คุณสมบัติหลัก)
- [ความต้องการของระบบ](#ความต้องการของระบบ)
- [การติดตั้ง](#การติดตั้ง)
- [การตั้งค่า](#การตั้งค่า)
- [การใช้งาน](#การใช้งาน)
- [การพัฒนา](#การพัฒนา)
- [การสนับสนุน](#การสนับสนุน)

## ✨ คุณสมบัติหลัก

- 🎫 **จัดการปัญหา**: สร้าง แก้ไข และติดตามสถานะปัญหา
- 👥 **จัดการผู้ใช้**: ระบบสิทธิ์ผู้ใช้ (Admin, Agent, User)
- 📊 **แดชบอร์ด**: แสดงสถิติและข้อมูลสรุป
- 🔔 **การแจ้งเตือน**: ระบบแจ้งเตือนแบบ Real-time
- 📁 **แนบไฟล์**: รองรับการแนบไฟล์ในปัญหา
- 📈 **รายงาน**: กราฟและสถิติรายเดือน/รายวัน
- 🎨 **UI สวยงาม**: ใช้ Tailwind CSS และ Font Awesome

## 🖥️ ความต้องการของระบบ

### ซอฟต์แวร์ที่จำเป็น
- **PHP** 8.1 หรือสูงกว่า
- **Composer** 2.0 หรือสูงกว่า
- **Node.js** 18.0 หรือสูงกว่า
- **npm** 8.0 หรือสูงกว่า
- **MySQL** 8.0 หรือสูงกว่า
- **Web Server** (Apache/Nginx)

### ส่วนขยาย PHP ที่จำเป็น
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 🚀 การติดตั้ง

### ขั้นตอนที่ 1: โคลนโปรเจค

```bash
# โคลนโปรเจคจาก Git
git clone https://github.com/your-username/helpdesk-system.git

# เข้าไปในโฟลเดอร์โปรเจค
cd helpdesk-system
```

### ขั้นตอนที่ 2: ติดตั้ง Dependencies

```bash
# ติดตั้ง PHP dependencies
composer install

# ติดตั้ง Node.js dependencies
npm install
```

### ขั้นตอนที่ 3: ตั้งค่า Environment

```bash
# คัดลอกไฟล์ environment
cp .env.example .env

# สร้าง application key
php artisan key:generate
```

### ขั้นตอนที่ 4: ตั้งค่าฐานข้อมูล

แก้ไขไฟล์ `.env` และตั้งค่าฐานข้อมูล:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=helpdesk_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### ขั้นตอนที่ 5: รัน Migration และ Seeder

```bash
# รัน migration เพื่อสร้างตาราง
php artisan migrate

# รัน seeder เพื่อเพิ่มข้อมูลเริ่มต้น
php artisan db:seed
```

### ขั้นตอนที่ 6: Build Assets

```bash
# Build CSS และ JavaScript
npm run build
```

### ขั้นตอนที่ 7: ตั้งค่าสิทธิ์

```bash
# ตั้งค่าสิทธิ์โฟลเดอร์ storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## ⚙️ การตั้งค่า

### การตั้งค่า Mail (สำหรับการแจ้งเตือน)

แก้ไขไฟล์ `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### การตั้งค่า Queue (สำหรับการประมวลผลแจ้งเตือน)

```bash
# รัน queue worker
php artisan queue:work
```

## 👤 การใช้งาน

### บัญชีเริ่มต้น

หลังจากรัน seeder แล้ว คุณสามารถเข้าสู่ระบบด้วยบัญชีต่อไปนี้:

#### ผู้ดูแลระบบ (Admin)
- **อีเมล**: admin@example.com
- **รหัสผ่าน**: password

#### เจ้าหน้าที่ (Agent)
- **อีเมล**: agent@example.com
- **รหัสผ่าน**: password

#### ผู้ใช้ทั่วไป (User)
- **อีเมล**: user@example.com
- **รหัสผ่าน**: password

### การใช้งานพื้นฐาน

1. **เข้าสู่ระบบ** ที่หน้า Login
2. **สร้างปัญหาใหม่** โดยคลิก "แจ้งปัญหาใหม่"
3. **ติดตามสถานะ** ในหน้า "รายการปัญหา"
4. **ดูสถิติ** ในหน้า "แดชบอร์ด"

## 🛠️ การพัฒนา

### การรัน Development Server

```bash
# รัน Laravel development server
php artisan serve

# รัน Vite development server (ใน terminal อีกตัว)
npm run dev
```

### การทดสอบ

```bash
# รัน unit tests
php artisan test

# รัน tests พร้อม coverage
php artisan test --coverage
```

### การตรวจสอบ Code Quality

```bash
# ตรวจสอบ coding standards
./vendor/bin/phpcs

# แก้ไข coding standards อัตโนมัติ
./vendor/bin/phpcbf
```

## 📁 โครงสร้างโปรเจค

```
helpdesk-system/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/             # Eloquent Models
│   ├── Notifications/      # Notification Classes
│   └── Jobs/              # Queue Jobs
├── database/
│   ├── migrations/         # Database Migrations
│   └── seeders/           # Database Seeders
├── resources/
│   ├── views/             # Blade Templates
│   ├── css/               # CSS Files
│   └── js/                # JavaScript Files
├── routes/                # Route Definitions
└── tests/                 # Test Files
```

## 🔧 การแก้ไขปัญหา

### ปัญหาที่พบบ่อย

#### 1. Permission Denied
```bash
# แก้ไขสิทธิ์โฟลเดอร์
chmod -R 775 storage bootstrap/cache
```

#### 2. Composer Memory Limit
```bash
# เพิ่ม memory limit
COMPOSER_MEMORY_LIMIT=-1 composer install
```

#### 3. Node Modules ไม่พบ
```bash
# ลบและติดตั้งใหม่
rm -rf node_modules package-lock.json
npm install
```


## 🙏 ขอบคุณ

ขอบคุณสำหรับการใช้ระบบจัดการปัญหานี้! หากโปรเจคนี้มีประโยชน์ กรุณาให้ ⭐ ใน GitHub repository

---

**พัฒนาโดย** [ต้นหนึ่ง]  
**อัปเดตล่าสุด** 2025