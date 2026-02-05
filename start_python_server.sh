#!/bin/bash
# -------------------------------
# Love Bumble Python Server Starter
# -------------------------------

# Activate virtual environment if exists
if [ -d "venv" ]; then
    echo "Activating virtual environment..."
    source venv/bin/activate
else
    echo "Virtual environment not found, starting system Python..."
fi

# Start the Python server
# Change server.py to your main server script
echo "Starting Python server..."
python3 server.py

# Optional: deactivate venv after server stops
if [ -d "venv" ]; then
    deactivate
fi
