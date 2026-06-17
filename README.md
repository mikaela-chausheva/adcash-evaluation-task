# Adcash Technical Assessment Tasks

Hey there! This repo contains three PHP tasks I built for a technical assessment. Each one tackles a different problem and shows off different skills. No fancy frameworks here - just clean, vanilla PHP doing what it does best.

## What's Inside?

### Task 1: Advertising Bid Auction
**The Challenge**: Build a second-price auction system where the highest bidder wins but pays the second-highest price.

Think of it like eBay - you bid $100, but if the next highest bid is $75, you only pay $75. Read bids from a CSV, figure out who wins, and what they pay.

[Check it out →](task1_advertising_bid_auction/)

### Task 2: Word Frequency Counter
**The Challenge**: Create a REST API that counts how many times words appear in text.

Send it some text like "love grows where kindness lives" and it'll tell you each word appeared once. Send more text with "kindness" in it, and the count goes up! It remembers everything and can handle multiple people using it at once.

[Check it out →](task2_word_frequency_counter/)

### Task 3: Escape a Labyrinth
**The Challenge**: Find the shortest path through a maze where you can break one wall.

You start at the top-left, need to reach the bottom-right, and can smash through one wall along the way. What's the quickest route? Uses BFS to figure it out.

[Check it out →](task3_escape_a_labyrinth/)

## Requirements

Just PHP 7.4 or newer. That's it! No composer, no frameworks, no dependencies. Keep it simple.

```bash
# Check your PHP version
php --version
```

## Quick Start

Each task lives in its own folder with everything it needs:

```bash
# Task 1: Run the auction
cd task1_advertising_bid_auction
php bid_auction.php sample.csv

# Task 2: Start the API server
cd task2_word_frequency_counter
php -S localhost:8080 index.php

# Task 3: Solve the maze
cd task3_escape_a_labyrinth
php solution.php
```

## The Tasks Explained

### Task 1: Advertising Bid Auction 🏷️

**What it does**: Reads a CSV file with ad bids and tells you who wins and what they pay.

**Example**:
```bash
# Your CSV has these bids:
# Ad 1: $0.50
# Ad 2: $33.00
# Ad 3: $12.00
# Ad 4: $33.50

php bid_auction.php bids.csv
# Output: 4, 33
# (Ad 4 wins but only pays $33, not $33.50)
```

**Why it's cool**:
- Single-pass algorithm - reads the file once, O(n) time
- Handles decimals, duplicates, empty lines, you name it
- Super fast - tested with 10,000+ rows
- 30 unit tests covering every edge case

**Skills shown**: Algorithms, file handling, CSV parsing, optimization

---

### Task 2: Word Frequency Counter 📊

**What it does**: A simple REST API that counts words. Submit text, get back word counts. It's persistent - restart the server, your data's still there.

**Example**:
```bash
# Start it up
php -S localhost:8080 index.php

# Send some text
curl -X POST http://localhost:8080/texts \
  -H "Content-Type: application/json" \
  -d '{"text": "Love grows where kindness lives."}'

# Response shows all current word counts:
# {
#   "frequencies": {
#     "love": 1,
#     "grows": 1,
#     "where": 1,
#     "kindness": 1,
#     "lives": 1
#   }
# }

# Get all words
curl http://localhost:8080/words

# Find a specific word
curl "http://localhost:8080/words/search?word=kindness"
```

**Why it's cool**:
- Thread-safe with file locking - multiple users can use it at once
- O(1) word lookups using hashmaps
- Case-insensitive (Love = LOVE = love)
- Strips out punctuation automatically
- 26 comprehensive tests
- Data persists in JSON file

**Skills shown**: REST APIs, concurrency, data persistence, text processing

---

### Task 3: Escape a Labyrinth 🗺️

**What it does**: Finds the shortest path through a maze where you can break through one wall.

**Example**:
```php
// 0 = open space, 1 = wall
$maze = [
    [0, 0, 0, 0, 0, 0],
    [1, 1, 1, 1, 1, 0],
    [0, 0, 0, 0, 0, 0],
    [0, 1, 1, 1, 1, 1],
    [0, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0]
];

echo solution($maze); // Output: 11
```

**Why it's cool**:
- Uses BFS (Breadth-First Search) for guaranteed shortest path
- Smart state tracking: same position but different "can I still break a wall?" status = different state
- O(height × width) complexity
- Works on mazes up to 20×20
- Full test suite with multiple scenarios

**Skills shown**: Graph algorithms, BFS, state space search, complexity analysis

---

## Running Tests

Every task has tests. Run them to see everything works:

```bash
# Task 1 (30 tests)
cd task1_advertising_bid_auction && php test_bid_auction.php

# Task 2 (26 tests)
cd task2_word_frequency_counter && php test.php

# Task 3 (Multiple test cases)
cd task3_escape_a_labyrinth && php tests.php
```

You should see a bunch of green checkmarks and "All tests passed!" messages.

## What Each Task Shows

**Algorithms & Problem Solving**:
- BFS for shortest paths (Task 3)
- Efficient single-pass processing (Task 1)
- Hashmap optimization for fast lookups (Task 2)

**Software Engineering**:
- REST API design (Task 2)
- Input validation everywhere
- Error handling with helpful messages
- File I/O operations (Tasks 1 & 2)
- Concurrent access handling (Task 2)

**Code Quality**:
- 76+ total unit tests across all tasks
- Every edge case covered
- Clear, readable code with comments
- Documentation for everything

## Project Structure

```
adcash_tasks/
│
├── README.md (you are here!)
│
├── task1_advertising_bid_auction/
│   ├── bid_auction.php              # The main program
│   ├── test_bid_auction.php         # 30 unit tests
│   ├── sample.csv                   # Example to try
│   └── README.md                    # Full docs
│
├── task2_word_frequency_counter/
│   ├── index.php                    # API entry point
│   ├── WordFrequencyCounter.php     # The brain
│   ├── test.php                     # 26 tests
│   ├── QUICKSTART.md                # Quick reference
│   └── README.md                    # Full docs
│
└── task3_escape_a_labyrinth/
    ├── solution.php                 # BFS pathfinding
    ├── tests.php                    # Test suite
    ├── NOTES.md                     # Algorithm notes
    └── README.md                    # Full docs
```

## Performance

All tasks are optimized:

- **Task 1**: Reads 10,000+ rows in under 100ms
- **Task 2**: O(1) word searches, handles concurrent requests
- **Task 3**: Solves 20×20 mazes instantly

## A Few Notes

- **No frameworks**: All vanilla PHP, no dependencies to install
- **Well tested**: Comprehensive test coverage on all tasks
- **Production ready**: Proper error handling, validation, edge cases covered
- **Clear docs**: Each task has its own detailed README

## Why These Tasks?

Each one demonstrates different skills:

1. **Task 1**: Algorithm optimization and efficiency
2. **Task 2**: API design and concurrent programming
3. **Task 3**: Graph algorithms and problem-solving

Together they show I can handle data structures, algorithms, APIs, testing, and writing clean, maintainable code.

## Want More Details?

Check out the individual README files in each task folder. They have:
- Complete usage examples
- API documentation (Task 2)
- Algorithm explanations (Task 3)
- Test coverage details
- Performance benchmarks
- Troubleshooting tips

---

Built with vanilla PHP and attention to detail. No magic, just solid code.

Questions? Each task has detailed documentation. Dive in and have a look around!
