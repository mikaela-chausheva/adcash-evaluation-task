# Word Frequency Counter

Hey there! This is a simple word counting app built with plain PHP - no fancy frameworks, no complicated dependencies. Just clean, straightforward code that does one thing well: counting how many times words appear in text.

Think of it like this: you throw some text at it, and it tells you "hey, you used the word 'love' 5 times!" Pretty handy for text analysis, right?

## What Can It Do?

- **Counts words reliably** - Even when multiple people use it at the same time (thanks to file locking)
- **Remembers everything** - Your word counts stick around even if you restart the server
- **Doesn't care about CAPS** - "Love", "LOVE", and "love" are all counted together
- **Handles messy text** - Punctuation? Special characters? No problem, it cleans them up
- **No text size limits** - Paste a novel if you want, it'll handle it
- **Simple REST API** - Just use regular HTTP requests
- **Bullet-proof validation** - It checks your input and gives you helpful error messages
- **Well tested** - Comes with 26 unit tests to make sure everything works

## What You Need

Just PHP 7.4 or newer. That's it!

## Getting Started

### Step 1: Navigate to the project

```bash
cd task2_word_frequency_counter
```

### Step 2: Make sure the folder can save files

```bash
chmod 755 .
```

### Step 3: Fire up the server

The easiest way is using PHP's built-in server:

```bash
php -S localhost:8080 index.php
```

Now you can access it at `http://localhost:8080`

**Using a different port?** No problem:
```bash
php -S localhost:3000 index.php
```

**Have Apache or Nginx?** Just point your web server to `index.php` and you're good to go.

## How to Use It

### Sending Text to Count (POST)

**What it does:** You send some text, and it counts all the words and adds them to its memory.

**The endpoint:** `POST /texts`

**What to send:**
```json
{
  "text": "Love grows where kindness lives."
}
```

**What you get back:**
```json
{
  "message": "Text processed successfully",
  "text_length": 32,
  "total_unique_words": 5,
  "frequencies": {
    "love": 1,
    "grows": 1,
    "where": 1,
    "kindness": 1,
    "lives": 1
  }
}
```

**Try it out:**
```bash
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Love grows where kindness lives."}'
```

**Cool feature:** The response shows you the current state of all word counts right away, so you can see the cumulative results immediately!

### Getting All Word Counts (GET)

**What it does:** Shows you every word that's been counted and how many times each appeared.

**The endpoint:** `GET /words`

**What you get:**
```json
{
  "total_unique_words": 8,
  "frequencies": {
    "love": 1,
    "grows": 1,
    "where": 1,
    "kindness": 2,
    "lives": 2,
    "in": 1,
    "every": 1,
    "heart": 1
  }
}
```

**Try it:**
```bash
curl http://localhost:8080/words
```

### Finding a Specific Word (GET)

**What it does:** Want to know how many times "kindness" appeared? Just ask!

**The endpoint:** `GET /words/search?word=kindness`

**What you get:**
```json
{
  "word": "kindness",
  "frequency": 2
}
```

**Try it:**
```bash
curl "http://localhost:8080/words/search?word=kindness"
```

**Pro tip:** It doesn't care about uppercase/lowercase, so searching for "KINDNESS" or "KiNdNeSs" works the same!

### Health Check (GET)

**What it does:** Just checks if the app is alive and kicking.

**The endpoint:** `GET /health`

**What you get:**
```json
{
  "status": "healthy",
  "timestamp": "2026-06-15 10:30:45"
}
```

## Running the Tests

Want to make sure everything works? Run the tests:

```bash
php test.php
```

You should see something like:
```
===========================================
Running tests...
===========================================

Test 1: Basic word counting
✓ 'love' appears 1 time
✓ 'grows' appears 1 time
✓ 5 unique words

...

===========================================
Results
===========================================
Total: 26
Passed: 26 ✓
Failed: 0 ✗
Success: 100%
===========================================
```

## Let's See It In Action

Here's a complete example showing how word counts accumulate:

**1. Send your first text:**
```bash
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Love grows where kindness lives."}'
```

**What's stored now:**
```
love: 1
grows: 1
where: 1
kindness: 1
lives: 1
```

**2. Send some more text:**
```bash
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Kindness lives in every heart."}'
```

**What's stored now (notice "kindness" and "lives" went up!):**
```
love: 1
grows: 1
where: 1
kindness: 2  ← went up!
lives: 2     ← went up!
in: 1        ← new word
every: 1     ← new word
heart: 1     ← new word
```

**3. Check all words:**
```bash
curl http://localhost:8080/words
```

**4. Search for a specific word:**
```bash
curl "http://localhost:8080/words/search?word=kindness"
# Returns: {"word": "kindness", "frequency": 2}
```

## What's Inside?

```
task2_word_frequency_counter/
├── index.php                    # The main API (handles all requests)
├── WordFrequencyCounter.php     # The brain (does the counting)
├── test.php                     # Makes sure everything works
├── example_usage.sh             # Examples you can run
├── README.md                    # You are here!
└── word_frequencies.json        # Where the data lives
```

## How It Works Under the Hood

### The Smart Stuff

I'm using a **hashmap** (PHP's associative array) to store words and their counts. Why? Because it's super fast!

- Looking up a word: **O(1)** - basically instant
- Adding/updating a word: **O(1)** - also instant
- Storage needed: **O(n)** - where n is how many unique words you have

### How It Counts Words

1. **Cleaning the text**: Uses a regex pattern `/\b[a-zA-Z]+\b/` to grab only actual words (letters only)
2. **Making it lowercase**: Everything becomes lowercase so "Love" and "love" match
3. **Counting**: Goes through each word and updates the count
4. **Time it takes**: O(m) where m is how long your text is - pretty efficient!

### Saving Your Data

All the word counts are saved in a JSON file. Every time you add text or check counts, it:
1. Opens the file
2. Locks it (so nobody else can mess with it at the same time)
3. Reads or writes the data
4. Unlocks it
5. Closes it

This means multiple people can use it at once without corrupting the data. Nice!

### Thread Safety (The Technical Bit)

Uses PHP's `flock()` to keep things safe:
- **LOCK_SH (Shared Lock)**: Multiple people can read at once
- **LOCK_EX (Exclusive Lock)**: Only one person can write at a time
- This prevents race conditions and data corruption

### Validation & Error Handling

The app checks everything:
- ✅ Is it the right type of request? (GET/POST)
- ✅ Is the JSON valid?
- ✅ Are all required fields there?
- ✅ Is the text actually text?
- ✅ Are strings not empty?

**Errors you might see:**
- **400 Bad Request** - You sent something wrong (it'll tell you what)
- **404 Not Found** - Wrong URL/endpoint
- **500 Internal Server Error** - Something unexpected happened (it logs this for debugging)

## A Few Things to Know

### What Counts as a "Word"?

Only sequences of letters (a-z, A-Z). So:
- "Hello" = word ✓
- "test123" = not a word ✗
- "@user" = not a word ✗

### Case Doesn't Matter

"Love", "LOVE", and "lOvE" are all the same word.

### Punctuation Gets Removed

"Hello!" and "Hello" are counted as the same word.

### Language Support

Works great with English and other Latin-alphabet languages. Other alphabets (Cyrillic, Arabic, Chinese, etc.) won't work properly.

### Data Storage

Everything's saved in a simple JSON file. Easy to read, easy to back up!

### Multiple Users

Yes! The file locking makes sure multiple people can use it at once. Under *extreme* load it might slow down a tiny bit, but for normal use it's totally fine.

### Single Server

This version runs on one server. If you need it on multiple servers, you'd want to swap the JSON file for a real database.

## Current Limitations

Being honest about what this *doesn't* do:

1. **No login/authentication** - Anyone can use it (fine for dev, but you'd want auth in production)
2. **No rate limiting** - You could spam it with requests (not ideal for public use)
3. **One JSON file** - For millions of words, you'd want a database instead
4. **Only letters count** - Numbers and special characters are ignored
5. **Loads text into memory** - For *really* huge texts (like gigabytes), you'd need streaming
6. **File locking bottleneck** - Under crazy high traffic, the file locking could slow things down
7. **No way to delete words** - Once counted, words stay (unless you clear everything)

## Edge Cases I've Handled

✅ Empty text (rejected with helpful error)
✅ Broken JSON (tells you what's wrong)
✅ Missing fields (tells you what's missing)
✅ Searching for a word that doesn't exist (returns 0)
✅ Mixed case variations (normalized)
✅ Punctuation everywhere (cleaned up)
✅ Really long texts (handles it)
✅ Multiple users at once (file locking)
✅ Server restarts (data persists)
✅ Empty search terms (rejected)

## Performance

Here's how fast things are:

- **Adding text**: O(m) - depends on text length
- **Getting all words**: O(n) - depends on unique word count
- **Finding one word**: O(1) - instant!
- **Storage**: O(n) - grows with unique words

## Troubleshooting

### "Permission denied" error?

Fix the folder permissions:
```bash
chmod 755 .
```

### Port 8080 already in use?

Just pick a different port:
```bash
php -S localhost:3000 index.php
```

### Tests failing?

Clean up and try again:
```bash
rm -f test_word_frequencies.json
php test.php
```

### Data not saving?

Check if the file can be created:
```bash
touch word_frequencies.json
chmod 644 word_frequencies.json
```

## Ideas for Future Improvements

If I were to build this for real production use, I'd add:

1. **Database** - MySQL, PostgreSQL, or Redis instead of JSON
2. **User accounts** - Login and authentication
3. **Rate limiting** - Prevent abuse
4. **Caching** - Redis/Memcached for speed
5. **API versioning** - Like `/v1/texts` for backwards compatibility
6. **Better logging** - Track errors and usage
7. **Monitoring** - See how it's performing
8. **Docker** - Easy deployment anywhere
9. **API docs** - Swagger/OpenAPI documentation
10. **More features** - Delete words, reset counts, export data, etc.

## Final Notes

This was built as a technical assessment solution. The goal was to create something clean, well-tested, and easy to understand. I hope the code speaks for itself!

Feel free to use, modify, or learn from this project. If you have questions about any part of it, the code has comments and the structure is straightforward.

Happy word counting! 📊
