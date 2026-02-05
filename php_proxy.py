"""
LoveBumble PHP API Proxy
Simple Flask server to mock/route PHP backend endpoints (login, register, etc.)
Run: python php_proxy.py
"""
from flask import Flask, request, jsonify
import sqlite3
import json
from datetime import datetime

app = Flask(__name__)
app.config['JSON_SORT_KEYS'] = False

# ===== Mock API Endpoints (replace with actual DB queries) =====

@app.route('/backend/auth/register.php', methods=['POST'])
def register():
    """Mock register endpoint"""
    data = request.get_json() or {}
    return jsonify({
        "success": True,
        "message": "User registered successfully",
        "user_id": 1
    })

@app.route('/backend/auth/login.php', methods=['POST'])
def login():
    """Mock login endpoint"""
    data = request.get_json() or {}
    return jsonify({
        "success": True,
        "message": "Login successful",
        "user_id": 1,
        "token": "mock_jwt_token_12345"
    })

@app.route('/backend/auth/logout.php', methods=['POST'])
def logout():
    """Mock logout endpoint"""
    return jsonify({
        "success": True,
        "message": "Logged out successfully"
    })

@app.route('/backend/users/get_user.php', methods=['GET'])
def get_user():
    """Mock get user endpoint"""
    user_id = request.args.get('user_id', 1)
    return jsonify({
        "success": True,
        "user": {
            "id": int(user_id),
            "username": "testuser",
            "email": "test@lovebumble.com",
            "age": 25,
            "bio": "Love Bumble user"
        }
    })

@app.route('/backend/chat/save_message.php', methods=['POST'])
def save_message():
    """Mock save message endpoint"""
    data = request.get_json() or {}
    return jsonify({
        "success": True,
        "message_id": 123,
        "timestamp": datetime.now().isoformat()
    })

@app.route('/backend/match/like_user.php', methods=['POST'])
def like_user():
    """Mock like user endpoint"""
    data = request.get_json() or {}
    return jsonify({
        "success": True,
        "message": "User liked"
    })

# ===== Proxy HTML files =====

@app.route('/', methods=['GET'])
def index():
    """Serve index.html"""
    with open('frontend/index.html', 'r', encoding='utf-8') as f:
        return f.read()

@app.route('/<path:filename>', methods=['GET'])
def serve_file(filename):
    """Serve frontend files (CSS, JS, HTML)"""
    try:
        with open(f'frontend/{filename}', 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        return jsonify({"error": "File not found"}), 404

# ===== Run =====
if __name__ == '__main__':
    print("[*] Starting LoveBumble PHP Proxy Server on http://localhost:8080")
    app.run(host='127.0.0.1', port=8080, debug=True)
