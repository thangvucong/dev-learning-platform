# Dev Learning Platform

Nền tảng Laravel CRM bán khóa học: quản trị khóa học, đơn hàng, lớp học, lịch học, thanh toán (OnePay / SePay QR), và tài liệu học tập.

Tài liệu này giúp developer mới clone source và chạy được project từ đầu.

---

## 1. Yêu cầu môi trường

### Bắt buộc


| Thành phần         | Phiên bản khuyến nghị                   | Ghi chú                                                   |
| ------------------ | --------------------------------------- | --------------------------------------------------------- |
| **PHP**            | 8.0 trở lên (Docker image dùng **8.4**) | `composer.json` khai báo `^7.3|^8.0`; khuyến nghị PHP 8.x |
| **Composer**       | 2.x                                     | Docker image copy từ `composer:2`                         |
| **Node.js**        | 16 LTS hoặc 18 LTS                      | Project **không** ghi `engines` trong `package.json`      |
| **npm**            | 8+ (tương thích `lockfileVersion: 3`)   |                                                           |
| **MySQL**          | 8.0                                     | `docker-compose.yml` dùng `mysql:8.0`                     |
| **Docker**         | 20.10+                                  | Tùy chọn nhưng khuyến nghị cho setup đồng nhất            |
| **Docker Compose** | v2 (`docker compose`)                   | File `docker-compose.yml` có sẵn                          |


Project **không** dùng PostgreSQL. **Không** dùng Vite — frontend build bằng **Laravel Mix 6**.

### Extension PHP cần thiết

Cài trên máy host (nếu chạy PHP ngoài Docker) hoặc dùng image Docker đã cấu hình sẵn:

- `pdo_mysql`
- `mbstring`
- `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo` (Laravel)
- `gd` (upload ảnh)
- `zip`
- `exif`, `pcntl`, `bcmath` (theo `Dockerfile`)
- `redis` (khi bật queue/cache qua Redis)

### Tùy chọn (tính năng nâng cao)

- **Redis 7** — có trong Docker; mặc định `QUEUE_CONNECTION=sync`, `CACHE_DRIVER=file`
- **Google OAuth** — đăng nhập Google (`GOOGLE_`*)
- **Google Drive** — tài liệu học tập (`GOOGLE_DRIVE_`*)
- **OnePay** — cổng thanh toán thẻ (`ONEPAY_`*)
- **SePay QR** — có `config/sepay.php` nhưng **chưa** có biến trong `.env.example` (xem mục 14)

---

## 2. Clone source

```bash
git clone https://github.com/thangvucong/dev-learning-platform.git
cd dev-learning-platform
```

Thay `<repository-url>` bằng URL Git thực tế của team (chưa có trong repo).

---

## 3. Cấu hình môi trường

```bash
cp .env.example .env
```

Chỉnh các biến quan trọng:

### Database

**Chạy bằng Docker** (khuyến nghị):

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=learning
DB_USERNAME=learning
DB_PASSWORD=123456
DB_ROOT_PASSWORD=123456
```

Giá trị mặc định khớp `docker-compose.yml` (`MYSQL_*`). Có thể đổi tên DB/user/password miễn là đồng bộ với service `db`.

**Chạy PHP trên máy host, MySQL cài local:**

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_learning
DB_USERNAME=root
DB_PASSWORD=
```

Tạo database thủ công trước khi migrate (mục 7).

### APP_URL

```env
APP_URL=http://localhost:8088
APP_PORT=8088
```

- Docker map nginx: `${APP_PORT:-8088}:80`
- Nếu dùng `php artisan serve`: đặt `APP_URL=http://127.0.0.1:8000` và chạy serve tương ứng

### Mail (tùy chọn)

`.env.example` mặc định SMTP trỏ `mailhog:1025` — **không** có service Mailhog trong `docker-compose.yml`. Để test mail local:

- Chạy Mailhog riêng, hoặc
- Đổi sang SMTP thật / `MAIL_MAILER=log`

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue / Cache / Session

Mặc định dev đủ dùng:

```env
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
```

Khi cần job nền (ví dụ `AutoAssignEnrollmentClassJob`):

```env
QUEUE_CONNECTION=redis
REDIS_HOST=redis   # Docker: redis | Host: 127.0.0.1
```

Service `worker` trong Docker đã chạy `queue:work` và `schedule:work` qua Supervisor.

### Redis (Docker)

```env
REDIS_HOST=redis
REDIS_PORT=6379
```

Port map ra host: `127.0.0.1:6379` (đổi bằng `REDIS_PORT` nếu trùng).

### Thanh toán & OAuth (tùy chọn)

Chỉ cần khi test checkout / Google login / Drive:

- `ONEPAY_*` — sandbox trong `.env.example`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `GOOGLE_DRIVE_*` — upload tài liệu học tập

---

## 4. Cài dependency backend

**Trên máy host:**

```bash
composer install
```

**Trong container Docker** (sau `docker compose up -d`):

```bash
docker compose exec app composer install
```

---

## 5. Cài dependency frontend

Image Docker **không** cài Node.js — chạy npm trên **máy host**:

```bash
npm install
npm run dev
```

Build production:

```bash
npm run prod
```

Asset được Mix compile vào `public/js`, `public/css`, `public/assets`. View dùng helper `mix()`, không dùng Vite.

---

## 6. Generate app key

```bash
php artisan key:generate
```

Docker:

```bash
docker compose exec app php artisan key:generate
```

Lỗi `No application encryption key` → chạy lại bước này.

---

## 7. Chạy database

### Cách A — Docker (khuyến nghị)

```bash
docker compose up -d
```

Services:


| Service  | Vai trò                                   |
| -------- | ----------------------------------------- |
| `app`    | PHP-FPM 8.4                               |
| `nginx`  | Web, port `APP_PORT` (mặc định 8088)      |
| `db`     | MySQL 8.0                                 |
| `redis`  | Redis 7                                   |
| `worker` | Supervisor: `queue:work`, `schedule:work` |


Kiểm tra container:

```bash
docker compose ps
docker compose logs -f db
docker compose logs -f nginx
```

MySQL từ **máy host** (GUI client, artisan chạy ngoài container):

- Host: `127.0.0.1`
- Port: `3307` (mặc định `DB_PORT` map `127.0.0.1:3307:3306`)

### Cách B — Không dùng Docker

1. Cài MySQL 8.0 local.
2. Tạo database và user:

```sql
CREATE DATABASE dev_learning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dev_learning'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON dev_learning.* TO 'dev_learning'@'localhost';
FLUSH PRIVILEGES;
```

1. Cập nhật `.env` tương ứng.

---

## 8. Chạy migration và seeder

Setup mới (xóa toàn bộ bảng và seed lại):

```bash
php artisan migrate:fresh --seed
```

Chỉ migrate (giữ data):

```bash
php artisan migrate
php artisan db:seed
```

Docker:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

`DatabaseSeeder` gọi lần lượt: permissions, roles, users, courses, orders, enrollments, classes, sessions, v.v. (21 migration).

**Lưu ý:** `CourseClassStudentSeeder` tồn tại nhưng **không** được gọi trong `DatabaseSeeder`.

---

## 9. Chạy project local

### Docker + nginx

```bash
docker compose up -d
# Terminal khác — build asset (host)
npm run dev
```

Truy cập: **[http://localhost:8088](http://localhost:8088)** (hoặc port trong `APP_PORT`).

### Không Docker

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Mặc định: [http://127.0.0.1:8000](http://127.0.0.1:8000) — cập nhật `APP_URL` cho khớp.

### Phân quyền thư mục (Linux/macOS)

```bash
chmod -R ug+rwx storage bootstrap/cache
```

---

## 10. Tài khoản mặc định

Sau `db:seed`:


| Vai trò        | Email                      | Mật khẩu   | Ghi chú                    |
| -------------- | -------------------------- | ---------- | -------------------------- |
| **Admin**      | `admin@example.com`        | `12345678` | Cố định trong `UserSeeder` |
| **Student**    | email ngẫu nhiên (factory) | `12345678` | 3 user, role `student`     |
| **Instructor** | email ngẫu nhiên (factory) | `12345678` | 3 user, role `instructor`  |


Đăng nhập: `/login`  
Đăng ký thêm user: `/register` (mặc định Laravel Breeze).

Route theo role (sau login):

- Admin: `/admin/...`
- Giảng viên: `/teacher/...`
- Học viên: `/user/...`

**Không** có tài khoản student/instructor cố định email — xem email trong DB:

```bash
php artisan tinker
>>> \App\Models\User::role('student')->pluck('email');
>>> \App\Models\User::role('instructor')->pluck('email');
```

---

## 11. Các command thường dùng

```bash
# Cache & config
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Route
php artisan route:list

# Database
php artisan migrate:fresh --seed

# Queue (khi QUEUE_CONNECTION=redis)
php artisan queue:work --sleep=3 --tries=3

# Storage (upload avatar, ảnh editor)
php artisan storage:link

# Test
php artisan test
# hoặc
./vendor/bin/phpunit
```

Docker — thêm prefix:

```bash
docker compose exec app php artisan <command>
```

---

## 12. Upload / storage

Project lưu file public qua disk `public` (avatar profile, ảnh bài viết/editor).

Tạo symbolic link (bắt buộc nếu upload ảnh local):

```bash
php artisan storage:link
```

File lưu tại `storage/app/public`, truy cập qua URL `/storage/...`.

`public/storage` nằm trong `.gitignore` — mỗi máy dev cần chạy `storage:link` một lần.

Tài liệu học tập (learning materials) có thể dùng **Google Drive** — cấu hình `GOOGLE_DRIVE_`*, không thay thế `storage:link` cho upload local.

---

## 13. Troubleshooting

### Permission denied (`storage/`, `bootstrap/cache/`)

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Docker: đảm bảo volume mount `./:/var/www` và user trong container có quyền ghi.

### Database connection failed

- **Docker:** `DB_HOST=db`, không dùng `127.0.0.1` trong `.env` của container `app`.
- **Artisan trên host + DB trong Docker:** `DB_HOST=127.0.0.1`, `DB_PORT=3307`.
- Đợi MySQL healthy: `docker compose ps` — service `db` phải `healthy`.
- Kiểm tra `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` khớp `MYSQL_`* trong compose.

### `npm install` lỗi

- Dùng Node 16/18 LTS.
- Xóa và cài lại: `rm -rf node_modules package-lock.json && npm install` (cân nhắc giữ `package-lock.json` trên team).
- Trên Linux thiếu build tools: `sudo apt install build-essential`.

### `mix-manifest.json` not found / Mix manifest missing

Project dùng **Laravel Mix**, không phải Vite.

```bash
npm install
npm run dev   # hoặc npm run prod
```

Đảm bảo tồn tại `public/mix-manifest.json` và file trong `public/js`, `public/css`.

### `No application encryption key` (APP_KEY missing)

```bash
php artisan key:generate
```

### `storage:link` lỗi hoặc ảnh 404

```bash
php artisan storage:link
ls -la public/storage
```

Xóa link cũ rồi tạo lại: `rm public/storage && php artisan storage:link`

### Migration lỗi

- MySQL 8.0+, charset `utf8mb4`.
- `migrate:fresh --seed` trên DB dev trống.
- Xem log: `php artisan migrate --pretend` hoặc message SQL cụ thể.

### Port Docker bị trùng

- Đổi `APP_PORT` trong `.env` (nginx), ví dụ `8090`.
- Đổi `DB_PORT` nếu `3307` bị chiếm.
- Đổi `REDIS_PORT` nếu `6379` bị chiếm.
- `docker compose down` rồi `docker compose up -d`.

### Job không chạy nền

Mặc định `QUEUE_CONNECTION=sync` — job chạy đồng bộ. Để dùng worker:

1. `QUEUE_CONNECTION=redis`
2. `docker compose up -d worker` hoặc `php artisan queue:work`

### Mail không gửi

Không có Mailhog trong compose — cấu hình SMTP thật hoặc `MAIL_MAILER=log`.

---

## 14. Quy trình setup nhanh

### Docker (khuyến nghị)

```bash
git clone <repository-url> dev-learning-platform && cd dev-learning-platform

cp .env.example .env
# Chỉnh .env: DB_HOST=db, DB_* khớp docker-compose, APP_URL=http://localhost:8088

docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan storage:link

npm install && npm run dev
```

Mở [http://localhost:8088](http://localhost:8088) — đăng nhập `admin@example.com` / `12345678`.

### Local không Docker

```bash
git clone <repository-url> dev-learning-platform && cd dev-learning-platform

cp .env.example .env
# Tạo MySQL database, cập nhật DB_* và APP_URL=http://127.0.0.1:8000

composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link

npm install && npm run dev

php artisan serve
```

---

## 15. Ghi chú cho developer mới

- **Không** sửa code trong `vendor/` — dùng `composer require` / `composer update`.
- Mỗi người dùng file `**.env` riêng** — **không** commit `.env` (đã có trong `.gitignore`).
- Frontend: sửa source trong `resources/js`, `resources/css` — **không** chỉnh tay file đã build trong `public/js`, `public/css` nếu có thể chạy Mix.
- Chạy test trước khi commit khi đụng logic quan trọng: `php artisan test`.
- Vùng rủi ro cao (đọc kỹ trước khi sửa): thanh toán OnePay/SePay, gán lớp enrollment, phân quyền Spatie, lịch FullCalendar.
- `laravel/telescope` có trong dev dependencies — debug local (cần publish/migrate Telescope nếu bật).

---

## Thông tin cần bổ sung từ team

Các mục sau **chưa** có đủ trong repo — cần owner dự án cập nhật:


| Mục                                               | Trạng thái                                                    |
| ------------------------------------------------- | ------------------------------------------------------------- |
| URL Git clone chính thức                          | Chưa có trong README/repo                                     |
| Phiên bản Node/npm cố định (`.nvmrc` / `engines`) | Chưa khai báo                                                 |
| Biến môi trường **SePay** (`SEPAY_`*)             | Có `config/sepay.php`, thiếu trong `.env.example`             |
| Service Mailhog trong Docker                      | `.env.example` tham chiếu `mailhog` nhưng compose không có    |
| `DB_ROOT_PASSWORD`                                | Dùng trong `docker-compose.yml`, chưa có trong `.env.example` |
| Tài khoản student/instructor seed cố định         | Chỉ admin cố định; còn lại email random                       |
| `CourseClassStudentSeeder`                        | Có file, không gọi trong `DatabaseSeeder`                     |


