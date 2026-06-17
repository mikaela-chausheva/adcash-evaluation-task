#!/usr/bin/env php
<?php

require_once 'bid_auction.php';

// testing toolkit
class TestRunner
{
    private $pass = 0;
    private $fail = 0;
    private $files = [];

    // Check if something is true ????
    public function ok($cond, $msg)
    {
        // Example usage:
        // $t->ok(5 > 3, "5 should be greater than 3");
        // Output: ✓ 5 should be greater than 3
        if ($cond) {
            $this->pass++;
            echo "  ✓ {$msg}\n";
        } else {
            $this->fail++;
            echo "  ✗ FAIL: {$msg}\n";
        }
    }

    // Check if two values are equal
    public function eq($expected, $actual, $msg)
    {
     

        //       Example usage:
        // $result = 2 + 2;
        // $t->eq(4, $result, "Math should work");
        // Output: ✓ Math should work

        // $result = 2 + 2;
        // $t->eq(5, $result, "Math should work");
        // Output: ✗ FAIL: Math should work
        //         Expected: 5
        //         Got:      4
        if ($expected !== $actual) {
            echo "    Expected: " . var_export($expected, true) . "\n";
            echo "    Got:      " . var_export($actual, true) . "\n";
        }
        $this->ok($expected === $actual, $msg);
    }

    // Check if code throws an error
    public function throws($fn, $expected_msg, $msg)
    {
          // Example usage:
          // $t->throws(function() {
          //     throw new Exception("File not found");
          // }, "File not found", "Should error on missing file");
          // Output: ✓ Should error on missing file
        try {
            $fn();
            $this->fail++;
            echo "  ✗ FAIL: {$msg} (no exception)\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), $expected_msg) !== false) {
                $this->pass++;
                echo "  ✓ {$msg}\n";
            } else {
                $this->fail++;
                echo "  ✗ FAIL: {$msg}\n";
                echo "    Expected: {$expected_msg}\n";
                echo "    Got: {$e->getMessage()}\n";
            }
        }
    }

    // Create temporary test files
    public function file($name, $content)
    {
        // Example:
  // $t->file('test1.csv', "1, 10\n2, 20");
  // Creates a file called test1.csv with that content
        file_put_contents($name, $content);
        $this->files[] = $name;
    }

// Delete temporary files
    public function cleanup()
    {
        foreach ($this->files as $f) {
            if (file_exists($f)) unlink($f);
        }
    }

    public function summary()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Results: {$this->pass} passed, {$this->fail} failed\n";
        echo str_repeat("=", 50) . "\n";
        return $this->fail === 0 ? 0 : 1;
    }
}

$t = new TestRunner();

echo "Running Bid Auction Tests\n";
echo str_repeat("=", 50) . "\n\n";


// Basic Functionality
echo "Test 1: Basic functionality\n";
$t->file('test1.csv', "1, 0.5\n2, 33\n3, 12\n4, 33.5");
$auction = new BidAuction();
$result = $auction->process('test1.csv');
$t->eq(4, $result['ad_id'], "Winner should be ad 4");
$t->eq(33.0, $result['price'], "Price should be 33");
$output = BidAuction::format($result);
$t->eq("4, 33", $output, "Output format should be '4, 33'");

// What it does:
//   1. Creates a CSV file with the sample data
//   2. Runs the auction
//   3. Checks: Winner is ad 4? ✓
//   4. Checks: Price is 33? ✓
//   5. Checks: Output format is correct? ✓

//   Output:
//   Test 1: Basic functionality
//     ✓ Winner should be ad 4
//     ✓ Price should be 33
//     ✓ Output format should be '4, 33'


// Minimum Valid Case
echo "\nTest 2: Minimum valid case (2 entries)\n";
$t->file('test2.csv', "1, 10\n2, 20");
$auction = new BidAuction();
$result = $auction->process('test2.csv');
$t->eq(2, $result['ad_id'], "Winner should be ad 2");
$t->eq(10.0, $result['price'], "Price should be 10");

 // Why this test? An auction needs at least 2 bidders. This tests the minimum valid scenario.

 //  Data:
 //  Ad 1: 10
 //  Ad 2: 20 ← Winner
 //  Result: Ad 2 wins, pays 10



// Decimal Bid Values
echo "\nTest 3: Decimal bid values\n";
$t->file('test3.csv', "1, 10.5\n2, 20.75\n3, 15.25");
$auction = new BidAuction();
$result = $auction->process('test3.csv');
$t->eq(2, $result['ad_id'], "Winner should be ad 2");
$t->eq(15.25, $result['price'], "Price should be 15.25");
  // Why? Make sure decimals work, not just whole numbers.



// Duplicate Bid Values
echo "\nTest 4: Duplicate bid values\n";
$t->file('test4.csv', "1, 50\n2, 50\n3, 30");
$auction = new BidAuction();
$result = $auction->process('test4.csv');
$t->eq(50.0, $result['price'], "Price should be 50 (second occurrence)");

  // Why? What if two ads bid the same amount?

  // Data:
  // Ad 1: 50 ← First 50 (wins)
  // Ad 2: 50 ← Second 50 (becomes second_bid)
  // Ad 3: 30

  // Result: Ad 1 wins (first one), pays 50 (the duplicate)

// Zero Bid Value
  // Why? Is zero a valid bid? Yes! This tests edge cases.
echo "\nTest 5: Zero bid value\n";
$t->file('test5.csv', "1, 0\n2, 10\n3, 5");
$auction = new BidAuction();
$result = $auction->process('test5.csv');
$t->eq(2, $result['ad_id'], "Winner should be ad 2");
$t->eq(5.0, $result['price'], "Price should be 5");

// Large File Performance
echo "\nTest 6: Large file (10000 entries)\n";
$content = "";
for ($i = 1; $i <= 10000; $i++) {
    $bid = rand(1, 1000) / 10;
    $content .= "{$i}, {$bid}\n";
}
$content .= "10001, 1000\n10002, 999\n";
$t->file('test6.csv', $content);
$auction = new BidAuction();
$start = microtime(true);
$result = $auction->process('test6.csv');
$time = round((microtime(true) - $start) * 1000, 2);
$t->eq(10001, $result['ad_id'], "Winner should be ad 10001");
$t->eq(999.0, $result['price'], "Price should be 999");
echo "  Time: {$time}ms\n";
$t->ok($time < 1000, "Should process fast");

  // Why? Tests that your algorithm is efficient with large files. Creates 10,002 entries, measures processing time.

  // Expected: Should finish in under 1 second (1000ms)

 // Empty Lines
echo "\nTest 7: CSV with empty lines\n";
$t->file('test7.csv', "1, 10\n\n2, 20\n\n3, 15\n");
$auction = new BidAuction();
$result = $auction->process('test7.csv');
$t->eq(2, $result['ad_id'], "Should handle empty lines");
  // Why? Real CSV files often have blank lines. Make sure we handle them gracefull


 // Whitespace Handling
echo "\nTest 8: Whitespace handling\n";
$t->file('test8.csv', " 1 , 10.5 \n 2 , 20.75 \n 3 , 15.25 ");
$auction = new BidAuction();
$result = $auction->process('test8.csv');
$t->eq(2, $result['ad_id'], "Should trim whitespace");
// Why? CSV files might have extra spaces. The trim() function should handle this.


// Error Handling
// Tests 9 - 15

// File not found
echo "\nTest 9: File not found\n";
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('nonexistent.csv');
}, "File not found", "Should throw for missing file");

// Invalid CSV format
echo "\nTest 10: Invalid CSV format\n";
$t->file('test10.csv', "1, 10, extra\n2, 20");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test10.csv');
}, "Invalid CSV format", "Should throw for wrong columns");

// Invalid ad_id
echo "\nTest 11: Invalid ad_id\n";
$t->file('test11.csv', "abc, 10\n2, 20");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test11.csv');
}, "Invalid ad_id", "Should throw for non-numeric ad_id");

 // Invalid bid
echo "\nTest 12: Invalid bid\n";
$t->file('test12.csv', "1, abc\n2, 20");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test12.csv');
}, "Invalid bid", "Should throw for non-numeric bid");

// Negative bid
echo "\nTest 13: Negative bid\n";
$t->file('test13.csv', "1, -10\n2, 20");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test13.csv');
}, "bid cannot be negative", "Should throw for negative bid");

// Insufficient entries
echo "\nTest 14: Insufficient entries\n";
$t->file('test14.csv', "1, 10");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test14.csv');
}, "At least 2 valid entries", "Should throw for < 2 entries");

// Completely empty
echo "\nTest 15: Empty file\n";
$t->file('test15.csv', "");
$auction = new BidAuction();
$t->throws(function() use ($auction) {
    $auction->process('test15.csv');
}, "No valid entries", "Should throw for empty file");


// Output Formatting
echo "\nTest 16: Output formatting\n";
$t->eq("1, 10", BidAuction::format(['ad_id' => 1, 'price' => 10.0]), "Integer format");
$t->eq("2, 10.5", BidAuction::format(['ad_id' => 2, 'price' => 10.5]), "Decimal format");
$t->eq("3, 10.25", BidAuction::format(['ad_id' => 3, 'price' => 10.25]), "Multiple decimals");
  // Why? Make sure 10.0 displays as "10" not "10.0", but 10.5 stays "10.5"

// Large Bid Values
echo "\nTest 17: Large bid values\n";
$t->file('test17.csv', "1, 999999.99\n2, 1000000\n3, 500000");
$auction = new BidAuction();
$result = $auction->process('test17.csv');
$t->eq(2, $result['ad_id'], "Winner should be ad 2");
$t->eq(999999.99, $result['price'], "Price should be 999999.99");
  // Why? Test with big numbers (million-dollar bids)



// Best Bid at Beginning
echo "\nTest 18: Best bid at beginning\n";
$t->file('test18.csv', "1, 100\n2, 50\n3, 75");
$auction = new BidAuction();
$result = $auction->process('test18.csv');
$t->eq(1, $result['ad_id'], "Winner should be ad 1");
$t->eq(75.0, $result['price'], "Price should be 75");
  // Why? Make sure the algorithm works even when the highest bid is the first line

echo "\n";
$t->cleanup();
exit($t->summary());
