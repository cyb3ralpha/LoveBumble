"""
Minimal profanity utilities used by realtime.chat.group_chat.
"""
import re
from typing import Pattern

# -------------------------------
# Simple Profanity List
# -------------------------------
_PROFANITY = {"damn", "hell", "badword"}  # adjust/extend to your needs
_PATTERN: Pattern = re.compile(r"\b(" + "|".join(re.escape(w) for w in _PROFANITY) + r")\b", re.IGNORECASE)

# -------------------------------
# Functions
# -------------------------------

def contains_profanity(text: str) -> bool:
    """
    Check if the text contains any profane words.
    Returns True if profanity is detected.
    """
    if not text:
        return False
    return bool(_PATTERN.search(text))


def censor_text(text: str) -> str:
    """
    Replace profane words in the text with asterisks.
    Example: "You are badword1" -> "You are ******"
    """
    if not text:
        return text
    return _PATTERN.sub(lambda m: "*" * len(m.group(0)), text)

# -------------------------------
# Example Usage
# -------------------------------
if __name__ == "__main__":
    test_messages = [
        "Hello everyone!",
        "You are badword1!",
        "Nothing wrong here",
        "This is example2 text"
    ]

    for msg in test_messages:
        if contains_profanity(msg):
            print(f"Profanity detected! Original: '{msg}' -> Cleaned: '{censor_text(msg)}'")
        else:
            print(f"No profanity: '{msg}'")
