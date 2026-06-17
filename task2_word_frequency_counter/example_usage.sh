#!/bin/bash

# Example Usage Script for Word Frequency Counter API
# This script demonstrates how to use all the API endpoints

echo "=========================================="
echo "Word Frequency Counter - Example Usage"
echo "=========================================="
echo ""

# Check if server is running
echo "Checking if server is running..."
if curl -s http://localhost:8080/health > /dev/null 2>&1; then
    echo "✓ Server is running"
else
    echo "✗ Server is not running!"
    echo "Please start the server first:"
    echo "  php -S localhost:8080 index.php"
    exit 1
fi

echo ""
echo "=========================================="
echo "1. Submitting first text"
echo "=========================================="
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Love grows where kindness lives."}' \
  -w "\n"

echo ""
echo "=========================================="
echo "2. Submitting second text"
echo "=========================================="
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Kindness lives in every heart."}' \
  -w "\n"

echo ""
echo "=========================================="
echo "3. Getting all word frequencies"
echo "=========================================="
curl -s http://localhost:8080/words
echo ""

echo ""
echo "=========================================="
echo "4. Searching for specific word: 'kindness'"
echo "=========================================="
curl -s "http://localhost:8080/words/search?word=kindness"
echo ""

echo ""
echo "=========================================="
echo "5. Searching for specific word: 'love'"
echo "=========================================="
curl -s "http://localhost:8080/words/search?word=love"
echo ""

echo ""
echo "=========================================="
echo "6. Searching for non-existent word: 'missing'"
echo "=========================================="
curl -s "http://localhost:8080/words/search?word=missing"
echo ""

echo ""
echo "=========================================="
echo "7. Testing case insensitivity: 'KINDNESS'"
echo "=========================================="
curl -s "http://localhost:8080/words/search?word=KINDNESS"
echo ""

echo ""
echo "=========================================="
echo "Example usage completed!"
echo "=========================================="
