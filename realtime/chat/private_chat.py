"""
Private Chat Logic for LoveBumble
--------------------------------
Handles:
- Sending private messages
- Fetching private chat history
"""

from realtime.database import get_connection
from realtime.moderation.profanity_filter import (
    contains_profanity,
    censor_text
)


def send_private_message(sender_id: int, receiver_id: int, message: str) -> dict:
    """
    Store a private message after validation and moderation
    """

    if not sender_id or not receiver_id or not message:
        return {"success": False, "error": "Invalid input"}

    if sender_id == receiver_id:
        return {"success": False, "error": "Cannot message yourself"}

    # Profanity filtering
    if contains_profanity(message):
        message = censor_text(message)

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        # Verify sender exists
        cursor.execute("SELECT id FROM users WHERE id = %s", (sender_id,))
        if cursor.fetchone() is None:
            return {"success": False, "error": "Sender does not exist"}

        # Verify receiver exists (prevents FK crash)
        cursor.execute("SELECT id FROM users WHERE id = %s", (receiver_id,))
        if cursor.fetchone() is None:
            return {"success": False, "error": "Receiver does not exist"}

        # Insert message
        cursor.execute(
            """
            INSERT INTO private_messages
            (sender_id, receiver_id, message, created_at)
            VALUES (%s, %s, %s, NOW())
            """,
            (sender_id, receiver_id, message)
        )
        conn.commit()

        return {
            "success": True,
            "message": "Private message sent"
        }

    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }

    finally:
        cursor.close()
        conn.close()


def get_private_chat(user1_id: int, user2_id: int, limit: int = 50) -> list:
    """
    Fetch private chat history between two users
    """

    if user1_id == user2_id:
        return []

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute(
            """
            SELECT
                pm.id,
                pm.message,
                pm.created_at,
                pm.sender_id,
                pm.receiver_id
            FROM private_messages pm
            WHERE
                (pm.sender_id = %s AND pm.receiver_id = %s)
                OR
                (pm.sender_id = %s AND pm.receiver_id = %s)
            ORDER BY pm.created_at ASC
            LIMIT %s
            """,
            (user1_id, user2_id, user2_id, user1_id, limit)
        )

        return cursor.fetchall()

    except Exception as e:
        return [{"error": str(e)}]

    finally:
        cursor.close()
        conn.close()
