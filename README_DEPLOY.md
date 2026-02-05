# Deploy LoveBumble on Debian 13 (Docker)

Prerequisites on Debian 13:
- Docker Engine
- Docker Compose

Steps:
1. Copy repository to server, e.g. `/opt/lovebumble`.
2. Create an `.env` file with secrets (or set env vars):

```
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=love_bumble
MYSQL_USER=root
MYSQL_PASSWORD=rootpassword
```

3. Build and start services:

```bash
cd /opt/lovebumble
docker-compose -f docker-compose.prod.yml up -d --build
```

4. The site will be available on port 80. Socket.IO and backend are proxied by nginx.

Notes:
- For production, replace default passwords, add TLS (use certbot or provide TLS certs to nginx), and run backups for the database.
- If you prefer not to use Docker, install `php`, `mariadb`, and `python3` on the server and configure services manually.
