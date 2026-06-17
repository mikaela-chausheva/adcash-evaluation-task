# Adcash Technical Assessment Tasks

This repository contains three PHP solutions for a technical assessment. Each task demonstrates different problem-solving approaches and programming skills using vanilla PHP.

## Overview

### Task 1: Advertising Bid Auction
A second-price auction system that processes bids from a CSV file and determines the winner and their payment amount. The highest bidder wins but pays the second-highest price.

**Key features**: Single-pass algorithm, handles edge cases, comprehensive test coverage

[View details →](task1_advertising_bid_auction/)

### Task 2: Word Frequency Counter
A REST API that tracks word frequencies across submitted texts. Data persists between server restarts and supports concurrent access.

**Key features**: Thread-safe operations, case-insensitive matching, persistent storage

[View details →](task2_word_frequency_counter/)

### Task 3: Escape a Labyrinth
A pathfinding solution that finds the shortest route through a maze with the ability to break through one wall using BFS (Breadth-First Search).

**Key features**: Optimal path guaranteed, state-space search, efficient complexity

[View details →](task3_escape_a_labyrinth/)

## Requirements

- PHP 7.4 or newer
- No additional dependencies required

```bash
php --version
```

## Quick Start

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

## Running Tests

Each task includes comprehensive tests:

```bash
# Task 1 - 30 tests
cd task1_advertising_bid_auction && php test_bid_auction.php

# Task 2 - 26 tests
cd task2_word_frequency_counter && php test.php

# Task 3 - Multiple test cases
cd task3_escape_a_labyrinth && php tests.php
```

## Additional Tools

Task 2 includes a shell script (`example_usage.sh`) for automated API testing. As I'm currently studying for my LPI (Linux Professional Institute) certification, I created this tool to apply shell scripting skills to practical testing scenarios.

```bash
cd task2_word_frequency_counter
./example_usage.sh
```

The script demonstrates API endpoints, verifies server status, and automates common testing workflows - useful for CI/CD pipelines and demonstrations.

## Project Structure

```
adcash_tasks/
├── README.md
├── task1_advertising_bid_auction/
│   ├── bid_auction.php
│   ├── test_bid_auction.php
│   ├── sample.csv
│   └── README.md
├── task2_word_frequency_counter/
│   ├── index.php
│   ├── WordFrequencyCounter.php
│   ├── test.php
│   ├── example_usage.sh
│   └── README.md
└── task3_escape_a_labyrinth/
    ├── solution.php
    ├── tests.php
    └── README.md
```

## Documentation

Each task folder contains detailed documentation including:
- Implementation details
- Usage examples
- Algorithm explanations
- Test coverage information

---

Built with vanilla PHP. See individual task READMEs for complete documentation.
