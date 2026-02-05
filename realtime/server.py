"""
LoveBumble Realtime Server
---------------------------
- Handles REST API for group & private chat
- Handles Socket.IO real-time communication
- Integrates moderation filter
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_socketio import SocketIO, join_room, emit

# Ensure package imports work when running this file directly
import os
import sys
# Add project root so `realtime` package can be imported
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

from realtime.chat.group_chat import send_group_message, get_group_messages
from realtime.chat.private_chat import send_private_message, get_private_chat
from realtime.moderation.profanity_filter import contains_profanity, censor_text

# -------------------------------
# App & SocketIO Setup
# -------------------------------
app = Flask(__name__)
CORS(app)  # Allow front-end communication
app.config['SECRET_KEY'] = "supersecretkey"
socketio = SocketIO(app, cors_allowed_origins="*")

# -------------------------------
# REST API Endpoints
# -------------------------------

# 1️⃣ Group Chat Send
@app.route("/group/send", methods=["POST"])
def api_group_send():
    data = request.json
    user_id = data.get("user_id")
    message = data.get("message", "")

    if contains_profanity(message):
        message = censor_text(message)

    result = send_group_message(user_id, message)
    return jsonify(result)

# 2️⃣ Group Chat Fetch
@app.route("/group/messages", methods=["GET"])
def api_group_messages():
    limit = int(request.args.get("limit", 50))
    messages = get_group_messages(limit)
    return jsonify(messages)

# 3️⃣ Private Chat Send
@app.route("/private/send", methods=["POST"])
def api_private_send():
    data = request.json
    sender_id = data.get("sender_id")
    receiver_id = data.get("receiver_id")
    message = data.get("message", "")

    if contains_profanity(message):
        message = censor_text(message)

    result = send_private_message(sender_id, receiver_id, message)
    return jsonify(result)

# 4️⃣ Private Chat Fetch
@app.route("/private/messages", methods=["GET"])
def api_private_messages():
    user1_id = int(request.args.get("user1_id"))
    user2_id = int(request.args.get("user2_id"))
    limit = int(request.args.get("limit", 50))
    messages = get_private_chat(user1_id, user2_id, limit)
    return jsonify(messages)

# -------------------------------
# SocketIO Real-Time Chat
# -------------------------------

# Join Group Room
@socketio.on("join_group")
def handle_join_group(data):
    room = "global_group"
    join_room(room)
    emit("status", {"msg": f"{data['user_name']} joined the group!"}, room=room)

# Send Group Message
@socketio.on("send_group_message")
def handle_group_message(data):
    room = "global_group"
    user_id = data.get("user_id")
    user_name = data.get("user_name")
    message = data.get("message", "")

    if contains_profanity(message):
        message = censor_text(message)

    send_group_message(user_id, message)
    emit("receive_group_message", {"user_name": user_name, "message": message}, room=room)

# Send Private Message
@socketio.on("private_message")
def handle_private_message(data):
    sender_id = data.get("sender_id")
    receiver_id = data.get("receiver_id")
    message = data.get("message", "")

    if contains_profanity(message):
        message = censor_text(message)

    send_private_message(sender_id, receiver_id, message)

    # Private room for two users
    room = f"private_{min(sender_id, receiver_id)}_{max(sender_id, receiver_id)}"
    join_room(room)
    emit("receive_private_message", {"sender_id": sender_id, "message": message}, room=room)

# -------------------------------
# Run Server
# -------------------------------
if __name__ == "__main__":
    print("[✓] Starting LoveBumble Realtime Server...")
    socketio.run(app, host="0.0.0.0", port=5000, debug=True)
