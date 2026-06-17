# Advertising Bid Auction

A command-line PHP program that implements a second-price auction mechanism for advertising bids.

## Overview

In advertising bid auctions, the highest bidder wins, but pays the price of the second-highest bid. This program reads a CSV file containing ad bids and determines:
- The winning ad (highest bid)
- The price to pay (second-highest bid)

## Requirements

- PHP 7.0 or higher
- No external dependencies or frameworks required

## Installation

1. Clone or download this project
2. Ensure PHP is installed on your system:
   ```bash
   php --version
   ```

## Usage

### Basic Usage

```bash
php bid_auction.php <csv_file>
```

### Example

Given a CSV file `sample.csv`:
```
1, 0.5
2, 33
3, 12
4, 33.5
```

Run the program:
```bash
php bid_auction.php sample.csv
```

Output:
```
4, 33
```

**Explanation:** Ad ID 4 has the highest bid (33.5), so it wins the auction. However, according to the second-price auction mechanism, it only pays the second-highest bid (33).

## CSV File Format

The input CSV file must have the following format:
- Two columns: `ad_id` and `bid`
- No header row
- Values can be separated by commas with optional spaces

### Valid Examples

```
1, 10
2, 20.5
3, 15
```

```
1,10
2,20.5
3,15
```

### Requirements
- **ad_id**: Must be a positive integer
- **bid**: Must be a non-negative number (integer or decimal)
- Minimum 2 entries required for an auction
- Empty lines are ignored
- Maximum tested: 10,000+ rows

## Running Tests

The project includes comprehensive unit tests covering various scenarios.

### Run All Tests

```bash
php test_bid_auction.php
```

### Expected Output

```
Running Bid Auction Tests
==================================================

Test 1: Basic functionality
  ✓ Winner should be ad 4
  ✓ Price should be 33
  ✓ Output format should be '4, 33'

Test 2: Minimum valid case (2 entries)
  ✓ Winner should be ad 2
  ✓ Price should be 10

...

==================================================
Test Results:
  Passed: 30
  Failed: 0
  Total:  30
==================================================
```

### Test Coverage

The test suite includes:
1. Basic functionality (example from requirements)
2. Minimum valid case (2 entries)
3. Decimal bid values
4. Duplicate bid values
5. Zero bid values
6. Large file performance (10,000+ rows)
7. Empty lines handling
8. Whitespace handling
9. File not found error
10. Invalid CSV format error
11. Invalid ad_id error
12. Invalid bid error
13. Negative bid error
14. Insufficient entries error
15. Empty file error
16. Output formatting
17. Large bid values
18. Best bid at different positions

## Implementation Details

### Algorithm

The program uses an efficient single-pass algorithm:
- Time Complexity: O(n) - reads the file once
- Space Complexity: O(1) - only stores three variables (best bid, second-best bid, best ad ID)

### Data Structures

- Simple variables to track:
  - `bestAdId`: ID of the ad with the highest bid
  - `bestBid`: Highest bid value
  - `secondBestBid`: Second-highest bid value

### Logic Flow

1. Read CSV file line by line
2. For each valid entry:
   - If bid > current best: move best to second-best, update best
   - Else if bid > current second-best: update second-best
3. Return best ad ID and second-best bid

## Error Handling

The program validates input and provides clear error messages:

| Error | Message |
|-------|---------|
| Missing file | `File not found: <filename>` |
| Unreadable file | `File is not readable: <filename>` |
| Wrong column count | `Invalid CSV format at line X: expected 2 columns, found Y` |
| Invalid ad_id | `Invalid ad_id at line X: 'value' must be a positive integer` |
| Invalid bid | `Invalid bid at line X: 'value' must be a number` |
| Negative bid | `Invalid bid at line X: bid cannot be negative` |
| Too few entries | `At least 2 valid entries are required for an auction` |
| Empty file | `No valid entries found in the CSV file` |

## Assumptions

1. **File Format**: CSV with exactly 2 columns (ad_id, bid)
2. **No Header**: The CSV file does not contain a header row
3. **Unique Winner**: If multiple ads have the same highest bid, the first encountered wins
4. **Second-best Definition**: The second-highest bid value, which may equal the highest if there are duplicates
5. **Zero Bids**: Zero is considered a valid bid value
6. **Empty Lines**: Empty lines in the CSV are silently ignored
7. **Whitespace**: Leading and trailing whitespace is automatically trimmed
8. **File Size**: Optimized for files up to 10,000 rows, but can handle larger files

## Limitations

1. **Memory**: For extremely large files (millions of rows), consider streaming approaches, though current implementation handles 10,000+ rows efficiently
2. **Concurrent Access**: Not designed for concurrent file access
3. **File Format**: Only supports simple CSV format (no quoted fields, no embedded commas)
4. **Encoding**: Assumes UTF-8 encoding

## Performance

- **Small files** (< 100 rows): Near-instant processing
- **Medium files** (100-1,000 rows): < 10ms
- **Large files** (10,000+ rows): < 100ms

Tested on a standard development machine.

## Code Structure

```
advertising_bid_auction/
├── bid_auction.php          # Main program
├── test_bid_auction.php     # Unit tests
├── sample.csv               # Sample input file
└── README.md               # This file
```

## Example Use Cases

### Example 1: Simple Auction
```bash
# Create test file
echo -e "1, 10\n2, 20\n3, 15" > test.csv

# Run program
php bid_auction.php test.csv

# Output: 2, 15
```

### Example 2: Decimal Values
```bash
# Create test file
echo -e "1, 10.5\n2, 20.75\n3, 15.25" > test.csv

# Run program
php bid_auction.php test.csv

# Output: 2, 15.25
```

### Example 3: Error Handling
```bash
# Invalid file
php bid_auction.php missing.csv

# Output: Error: File not found: missing.csv
```

## Troubleshooting

### Permission Denied
```bash
chmod +x bid_auction.php
./bid_auction.php sample.csv
```

### PHP Not Found
Install PHP or specify the full path:
```bash
/usr/bin/php bid_auction.php sample.csv
```

### Unexpected Output
- Verify CSV format (2 columns, no header)
- Check for invalid characters in the file
- Ensure ad_id values are positive integers
- Ensure bid values are non-negative numbers

## License

This is a technical assessment solution and is provided as-is for evaluation purposes.

## Author

Created as a solution for the Advertising Bid Auction technical assessment.
