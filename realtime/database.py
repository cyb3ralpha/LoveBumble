import os
from dotenv import load_dotenv
import mysql.connector

# Load .env from project root
ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
load_dotenv(os.path.join(ROOT, ".env"))

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_USER = os.getenv("DB_USER", "root")
DB_PASS = os.getenv("DB_PASS", "")
DB_NAME = os.getenv("DB_NAME", "love_bumble")
DB_PORT = int(os.getenv("DB_PORT", "3306"))


def get_connection():
    """Return a MySQL connection using environment settings."""
    return mysql.connector.connect(
        host=DB_HOST,
        port=DB_PORT,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME,
        charset="utf8",
        use_pure=True,
    )
