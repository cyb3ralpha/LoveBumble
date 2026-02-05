# LoveBumble – Dating & Chat Platform

A full-stack dating and real-time chat application with match-making, profile management, and group/private messaging capabilities.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Project Structure](#project-structure)
- [Local Development](#local-development)
- [Configuration](#configuration)
- [Database](#database)
- [API Endpoints](#api-endpoints)
- [Frontend](#frontend)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Features

### User Management
- ✅ User registration & login with age verification
- ✅ Profile management (edit bio, upload pictures, interests)
- ✅ Age gating (minimum 18 years)
- ✅ Account settings & preferences

### Matching & Discovery
- ✅ Match-making algorithm (like/dislike users)
- ✅ Profile browsing & swiping interface
- ✅ Match history & favorites

### Messaging
- ✅ **Private Chat** – One-on-one messaging with real-time Socket.IO
- ✅ **Group Chat** – Public group messaging room
- ✅ **Random Chat** – Anonymous random pairing for chat
- ✅ Message history & persistence
- ✅ Profanity filtering with auto-censoring

### Moderation & Safety
- ✅ User reporting system
- ✅ Community guidelines & Terms of Service
- ✅ Privacy policy
- ✅ Profanity filter for all chat

---

## Tech Stack

### Frontend
- **HTML5, CSS3, JavaScript** (ES6+)
- **Socket.IO Client** for real-time WebSocket communication
- **Poppins Font** for consistent typography
- **Theme CSS** for unified styling

### Backend
- **PHP 8.1** (Apache or PHP built-in server)
- **Python 3.11** with Flask + Flask-SocketIO
- **eventlet** for async WebSocket support
- **mysql-connector-python** for database access

### Database
- **MariaDB 10.4** (MySQL-compatible)
- Persistent data storage for users, messages, matches

### Deployment
- **Docker & Docker Compose** (recommended)
- **Nginx** (reverse proxy, static file serving, SSL)
- **Systemd** (native service management alternative)

---

## Prerequisites

### For Local Development (Windows)

- **Git** (for version control)
- **Python 3.11+** (for realtime server)
- **PHP 8.1+** (optional; can run via Docker or php_proxy.py)
- **MariaDB 10.4+** (local instance or Docker)
- **Docker Desktop** (optional but recommended)

### For Deployment (Debian 13)

- **Docker** & **Docker Compose**
- **SSH access** to your server
- **8GB+ RAM, 20GB+ disk** recommended

---

## Project Structure

```
LoveBumble/
├── backend/                    # PHP backend
│   ├── auth/                   # Login, register, logout
│   ├── chat/                   # Message saving
│   ├── config/                 # Database configuration
│   ├── match/                  # Like/match endpoints
│   └── users/                  # User profile endpoints
├── frontend/                   # HTML5 / CSS3 / JavaScript
│   ├── index.html              # Home / landing page
│   ├── login.html              # Login form
│   ├── register.html           # Registration form
│   ├── dashboard.html          # User dashboard
│   ├── match.html              # Match discovery / swiping
│   ├── chat.html               # Private chat
│   ├── group-chat.html         # Group chat
│   ├── random-chat.html        # Anonymous random chat
│   ├── profile.html            # View profile
│   ├── edit-profile.html       # Edit profile
│   ├── settings.html           # Account settings
│   ├── report.html             # Report user
│   ├── verify-age.html         # Age verification
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript logic
│   └── assets/images/          # Images & icons
├── realtime/                   # Python Flask + Socket.IO server
│   ├── server.py               # Main server (REST API + WebSocket)
│   ├── requirements.txt        # Python dependencies
│   ├── database.py             # DB connection helper
│   ├── chat/                   # Group & private chat modules
│   └── moderation/             # Profanity filter
├── database/
│   └── schema.sql              # Database schema
├── docker/                     # Docker configurations
│   ├── php/Dockerfile
│   ├── realtime/Dockerfile
│   └── nginx/site.conf
├── docker-compose.yml          # Dev Docker Compose
├── docker-compose.prod.yml     # Production Docker Compose
├── .env                        # Environment variables (secrets)
├── .gitignore                  # Git ignore rules
└── README.md                   # This file
```

---

## Local Development

### Windows Setup

#### 1. Install Python Dependencies
```powershell
cd realtime
pip install -r requirements.txt
cd ..
```

#### 2. Configure Environment
Edit `.env`:
```env
APP_ENV=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=love_bumble
DB_USER=root
DB_PASS=rootpassword
```

#### 3. Start Services

**Option A: Python HTTP Server + Mock Backend (Easiest)**
```powershell
# Terminal 1: Frontend (port 8000)
cd frontend
python -m http.server 8000

# Terminal 2: Realtime server (port 5000)
cd ..\realtime
python -m realtime.server

# Terminal 3: PHP mock backend (port 8080)
cd ..
python php_proxy.py
```

Visit: http://localhost:8000

**Option B: Docker Compose**
```powershell
docker-compose up -d
cd frontend
python -m http.server 8000
```

Visit: http://localhost:8000

### macOS/Linux Setup

```bash
# Install dependencies
cd realtime
pip3 install -r requirements.txt
cd ..

# Start frontend
cd frontend
python3 -m http.server 8000 &

# Start realtime server
cd ../realtime
export PYTHONPATH=/path/to/LoveBumble
python3 -m realtime.server &

# Or use Docker
cd ..
docker-compose up -d
```

Visit: http://localhost:8000

---

## Configuration

### Environment Variables (`.env`)

```env
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=love_bumble
DB_USER=root
DB_PASS=rootpassword

# Application
APP_ENV=development
SITE_URL=http://localhost:8000
UPLOAD_DIR=uploads/

# Docker
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=love_bumble
```

### Frontend Config (`frontend/js/config.js`)

Automatically detects environment:
- **Local:** Uses ports 8080 (backend) & 5000 (realtime)
- **Production:** Uses relative paths & same-origin WebSocket

---

## Database

### Initialize

```bash
# Local
mysql -u root -p love_bumble < database/schema.sql

# Docker
docker exec lovebumble-db mysql -u root -p"rootpassword" love_bumble < database/schema.sql
```

### Tables
- `users` – User accounts & profiles
- `matches` – Like/dislike relationships
- `private_messages` – One-on-one chat
- `group_messages` – Public group chat
- `reports` – User reports for moderation

---

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/backend/auth/login.php` | Login user |
| POST | `/backend/auth/register.php` | Register new user |
| POST | `/backend/auth/logout.php` | Logout user |

### Users
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/backend/users/get_user.php?user_id=1` | Get user profile |
| POST | `/backend/users/update_user.php` | Update user profile |

### Matching
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/backend/match/like_user.php` | Like/match user |

### Real-Time (Socket.IO)
| Event | Description |
|-------|-------------|
| `send_group_message` | Send to public group |
| `receive_group_message` | Receive from group |
| `private_message` | Send 1-on-1 message |
| `receive_private_message` | Receive 1-on-1 message |

**WebSocket URLs:**
- Dev: `ws://localhost:5000`
- Prod: `ws://yourdomain.com` (via Nginx)

---

## Frontend

### Pages
1. **index.html** – Landing page
2. **login.html** – Login form
3. **register.html** – Registration form
4. **verify-age.html** – Age verification
5. **dashboard.html** – User hub
6. **match.html** – Profile browsing & swiping
7. **chat.html** – Private one-on-one chat
8. **group-chat.html** – Public group messaging
9. **random-chat.html** – Anonymous random chat
10. **profile.html** – View profile
11. **edit-profile.html** – Edit profile
12. **settings.html** – Account settings
13. **report.html** – Report user

### Styling
- **Theme:** Dark mode (navy/slate bg, rose/pink accent)
- **Font:** Poppins (Google Fonts)
- **Responsive:** Mobile-first design
- **CSS Files:**
  - `theme.css` – Global theme variables
  - `main.css` – General styles
  - `auth.css` – Login/register
  - `chat.css` – Chat UI
  - `match.css` – Match discovery
  - `profile.css` – Profile page

---

## Deployment

### Debian 13 with Docker (Recommended)

#### 1. Copy Project to Server
```bash
scp -r LoveBumble root@192.168.100.28:/opt/
# or
rsync -avz --delete LoveBumble/ root@192.168.100.28:/opt/lovebumble/
```

#### 2. SSH into Server
```bash
ssh root@192.168.100.28
cd /opt/LoveBumble
```

#### 3. Install Docker
```bash
apt-get update
apt-get install -y docker.io docker-compose
```

#### 4. Create Production `.env`
```bash
cat > .env << EOF
APP_ENV=production
DB_HOST=db
DB_PORT=3306
DB_NAME=love_bumble
DB_USER=lovebumble_user
DB_PASS=YOUR_SECURE_PASSWORD
MYSQL_ROOT_PASSWORD=YOUR_SECURE_ROOT_PASSWORD
MYSQL_DATABASE=love_bumble
MYSQL_USER=lovebumble_user
MYSQL_PASSWORD=YOUR_SECURE_PASSWORD
EOF
chmod 600 .env
```

#### 5. Start Services
```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

#### 6. Initialize Database
```bash
sleep 10
docker exec lovebumble-db mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < database/schema.sql
```

#### 7. Verify
```bash
docker ps
# Should show: db, php, realtime, nginx
```

Visit: `http://192.168.100.28/`

### Enable HTTPS (Let's Encrypt)

```bash
apt-get install -y certbot python3-certbot-nginx
certbot certonly --standalone -d yourdomain.com

# Update docker/nginx/site.conf to use certs
docker-compose -f docker-compose.prod.yml up -d --build
```

### Native Installation (Without Docker)

```bash
# Install dependencies
apt-get update
apt-get install -y nginx php-fpm php-mysql mysql-server python3 python3-pip

# Install Python packages
pip3 install -r realtime/requirements.txt

# Configure MySQL
mysql -u root -p
CREATE DATABASE love_bumble;
CREATE USER 'lovebumble_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL ON love_bumble.* TO 'lovebumble_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

mysql -u lovebumble_user -p love_bumble < database/schema.sql

# Configure Nginx
cp docker/nginx/site.conf /etc/nginx/sites-available/lovebumble
ln -s /etc/nginx/sites-available/lovebumble /etc/nginx/sites-enabled/
systemctl restart nginx

# Start PHP-FPM
systemctl start php-fpm

# Create systemd service for realtime server
# (See README_DEPLOY.md for full instructions)
```

---

## Troubleshooting

### Frontend Not Loading (http://localhost:8000)

```powershell
# Check if server is running
netstat -ano | findstr ":8000"

# Restart
cd frontend
python -m http.server 8000
```

### WebSocket Connection Failed

```bash
# Check realtime server
netstat -ano | findstr ":5000"

# Restart with debug output
$env:PYTHONPATH='C:\Users\ab587\Desktop\LoveBumble'
python -m realtime.server
```

### Database Connection Error

```bash
# Check MariaDB is running
docker ps | grep db

# Verify .env credentials
cat .env

# Re-import schema
docker exec lovebumble-db mysql -u root -p"${MYSQL_ROOT_PASSWORD}" ${MYSQL_DATABASE} < database/schema.sql
```

### Docker Container Crashes

```bash
# View logs
docker logs lovebumble-db
docker logs lovebumble-realtime

# Rebuild
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build
```

---

## Features Implemented

- ✅ User authentication (login/register)
- ✅ Age verification (18+)
- ✅ Profile management
- ✅ Match discovery & swiping
- ✅ Private real-time chat (Socket.IO)
- ✅ Group chat (Socket.IO)
- ✅ Random anonymous chat
- ✅ Message history (persistent database)
- ✅ Profanity filtering
- ✅ User reporting system
- ✅ Dark mode theme
- ✅ Mobile-responsive UI
- ✅ Docker-based deployment
- ✅ Multi-environment config (dev/prod)

---

## Known Limitations

- User uploads not fully integrated
- Email verification stub (no real email sending)
- Admin dashboard not implemented
- Analytics/statistics not implemented
- Payment/subscription system not included

---

## Future Enhancements

- [ ] Email notifications
- [ ] Two-factor authentication (2FA)
- [ ] Video call support (WebRTC)
- [ ] Location-based matching
- [ ] Social media login (OAuth)
- [ ] Admin panel for moderation
- [ ] Analytics dashboard
- [ ] Push notifications
- [ ] Dark/light theme toggle
- [ ] Multiple language support (i18n)

---

## Security Notes (Production)

1. ⚠️ **Change all default passwords** in `.env` immediately
2. ⚠️ **Enable HTTPS** with Let's Encrypt or your certificate authority
3. ⚠️ **Use strong database passwords** (20+ chars, mixed alphanumeric)
4. ⚠️ **Restrict database access** to localhost or internal network only
5. ⚠️ **Keep dependencies updated** regularly
6. ⚠️ **Implement rate limiting** on API endpoints
7. ⚠️ **Enable CORS properly** (restrict to your domain)
8. ⚠️ **Regular backups** of the database (daily recommended)

---

## Support

For issues or questions, please open an issue on GitHub or contact support.

---

## License

This project is proprietary and confidential. All rights reserved. Unauthorized copying or distribution is prohibited.

**Created:** February 2026  
**Last Updated:** February 5, 2026

---

**Happy dating! 💕**
