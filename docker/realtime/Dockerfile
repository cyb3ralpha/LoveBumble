FROM python:3.11-slim
WORKDIR /app

# Install dependencies
COPY realtime/requirements.txt /app/requirements.txt
RUN apt-get update && apt-get install -y build-essential libssl-dev libffi-dev && \
    pip install --no-cache-dir -r requirements.txt && \
    rm -rf /var/lib/apt/lists/*

# Copy app
COPY realtime /app/realtime
ENV PYTHONUNBUFFERED=1

EXPOSE 5000
CMD ["python", "-m", "realtime.server"]
