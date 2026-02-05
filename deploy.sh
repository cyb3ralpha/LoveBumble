#!/bin/bash
# LoveBumble Debian 13 Deployment Script
# Run this on your Debian 13 server after copying the project

set -e  # Exit on error

echo "=========================================="
echo "LoveBumble Deployment Script"
echo "=========================================="
echo ""

# Step 1: Update system
echo "[1/7] Updating system packages..."
apt-get update
apt-get upgrade -y

# Step 2: Install Docker
echo "[2/7] Installing Docker & Docker Compose..."
apt-get install -y curl
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh

# Install Docker Compose
apt-get install -y docker-compose

# Verify installation
echo "Docker version:"
docker --version
echo "Docker Compose version:"
docker-compose --version

# Step 3: Navigate to project
echo "[3/7] Setting up project directory..."
if [ ! -d "/opt/lovebumble" ]; then
    echo "ERROR: Project not found at /opt/lovebumble"
    echo "Please copy the LoveBumble directory to /opt/lovebumble first:"
    echo "  scp -r LoveBumble root@192.168.100.28:/opt/"
    echo "Then run this script again."
    exit 1
fi

cd /opt/lovebumble
pwd

# Step 4: Create production .env file
echo "[4/7] Creating production environment file..."
if [ -f ".env" ]; then
    echo "WARNING: .env already exists. Backing up to .env.bak"
    cp .env .env.bak
fi

cat > .env << 'EOF'
# LoveBumble Production Environment
APP_ENV=production
SITE_URL=http://192.168.100.28

# Database Configuration
DB_HOST=db
DB_PORT=3306
DB_NAME=love_bumble
DB_USER=lovebumble_user
DB_PASS=ChangeMe123!SecurePassword

# MySQL Root (for initialization only)
MYSQL_ROOT_PASSWORD=ChangeMe456!RootPassword
MYSQL_DATABASE=love_bumble
MYSQL_USER=lovebumble_user
MYSQL_PASSWORD=ChangeMe123!SecurePassword
EOF

chmod 600 .env
echo "✓ .env created. IMPORTANT: Edit .env and change the passwords!"
echo "  nano /opt/lovebumble/.env"

# Step 5: Build and start Docker containers
echo "[5/7] Building Docker images (this may take 5-10 minutes)..."
docker-compose -f docker-compose.prod.yml build

echo "[6/7] Starting services..."
docker-compose -f docker-compose.prod.yml up -d

# Wait for MariaDB to start
echo "Waiting for database to initialize (20 seconds)..."
sleep 20

# Step 6: Initialize database
echo "[7/7] Initializing database schema..."
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < database/schema.sql
echo "✓ Database initialized"

# Verify containers are running
echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
docker ps
echo ""
echo "Next steps:"
echo "1. Access the application: http://192.168.100.28/"
echo "2. (IMPORTANT) Change passwords in .env file:"
echo "   nano /opt/lovebumble/.env"
echo "3. For HTTPS setup, see README_DEPLOY.md"
echo ""
echo "To view logs:"
echo "  docker-compose -f docker-compose.prod.yml logs -f"
echo ""
echo "To restart services:"
echo "  cd /opt/lovebumble && docker-compose -f docker-compose.prod.yml restart"
echo ""
