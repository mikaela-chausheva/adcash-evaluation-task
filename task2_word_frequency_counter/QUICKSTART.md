# Quick Start Guide

## Start the Server

```bash
php -S localhost:8080 index.php
```

## Run the Tests

```bash
php test.php
```

## Test the API

### 1. Submit text (POST)
```bash
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Love grows where kindness lives."}'
```

### 2. Get all frequencies (GET)
```bash
curl http://localhost:8080/words
```

### 3. Search for a word (GET)
```bash
curl "http://localhost:8080/words/search?word=kindness"
```

## Run Example Script

```bash
./example_usage.sh
```

That's it! See [README.md](README.md) for full documentation.
