"""
Group Chat Logic for LoveBumble
--------------------------------
Handles:
- Sending group messages
- Fetching recent group messages
"""

from realtime.database import get_connection
from realtime.moderation.profanity_filter import (
    contains_profanity,
    censor_text
)


def send_group_message(user_id: int, message: str) -> dict:
    """
    Store a group message in database after validation & moderation
    """

    if not message or not user_id:
        return {"success": False, "error": "Invalid input"}

    # Profanity filtering
    if contains_profanity(message):
        message = censor_text(message)

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        # Verify user exists (prevents FK crash)
        cursor.execute(
            "SELECT id FROM users WHERE id = %s",
            (user_id,)
        )
        if cursor.fetchone() is None:
            return {"success": False, "error": "User does not exist"}

        # Insert message
        cursor.execute(
            """
            INSERT INTO group_messages (user_id, message, created_at)
            VALUES (%s, %s, NOW())
            """,
            (user_id, message)
        )
        conn.commit()

        return {
            "success": True,
            "message": "Group message sent"
        }

    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }

    finally:
        cursor.close()
        conn.close()


def get_group_messages(limit: int = 50) -> list:
    """
    Fetch recent group messages with user info
    """

    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute(
            """
            SELECT
                gm.id,
                gm.message,
                gm.created_at,
                u.id AS user_id,
                u.username
            FROM group_messages gm
            JOIN users u ON u.id = gm.user_id
            ORDER BY gm.created_at DESC
            LIMIT %s
            """,
            (limit,)
        )

        return cursor.fetchall()

    except Exception as e:
        return [{"error": str(e)}]

    finally:
        cursor.close()
        conn.close()
