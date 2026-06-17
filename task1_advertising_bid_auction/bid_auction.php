#!/usr/bin/env php
<?php

class BidAuction
{
    private $winner_id = null; // Which ad won
    private $top_bid = null;  // Highest bid
    private $second_bid = null; // Second-highest bid

    // Main logic
    public function process($file)
    {

        // file validation
        if (!file_exists($file)) {
            throw new Exception("File not found: {$file}");
        }

        if (!is_readable($file)) {
            throw new Exception("File is not readable: {$file}");
        }

        // Read file line by line 
        $fp = fopen($file, 'r'); // Open file for reading
        $line_num = 0;
        $count = 0;

        while (($line = fgets($fp)) !== false) { // Read one line at a time
            $line_num++;
            $line = trim($line);  // Remove whitespace

            // Parse each line
            if (empty($line)) continue;

            $parts = array_map('trim', explode(',', $line));

            if (count($parts) != 2) { // Must have exactly 2 values
                fclose($fp);
                throw new Exception("Invalid CSV format at line {$line_num}: expected 2 columns, found " . count($parts));
            }

            // Validate Data
            list($ad_id, $bid) = $parts; // Assign to variables

             // Check ad_id is a positive integer
            if (!ctype_digit($ad_id) || intval($ad_id) <= 0) {
                fclose($fp);
                throw new Exception("Invalid ad_id at line {$line_num}: '{$ad_id}' must be a positive integer");
            }   

            // Check bid is a number
            if (!is_numeric($bid)) {
                fclose($fp);
                throw new Exception("Invalid bid at line {$line_num}: '{$bid}' must be a number");
            }

            $ad_id = (int)$ad_id; // Convert "4" → 4
            $bid = (float)$bid;    // Convert "33.5" → 33.5

              // Check bid is not negative
            if ($bid < 0) {
                fclose($fp);
                throw new Exception("Invalid bid at line {$line_num}: bid cannot be negative");
            }


            // Initially: top_bid = null, second_bid = null, winner_id = null


            // update top bids
            if ($this->top_bid === null) {
                $this->top_bid = $bid;
                $this->winner_id = $ad_id;
            } elseif ($bid > $this->top_bid) {
                $this->second_bid = $this->top_bid;
                $this->top_bid = $bid;
                $this->winner_id = $ad_id;
            } elseif ($this->second_bid === null || $bid > $this->second_bid) {
                $this->second_bid = $bid;
            }

            $count++;

            // Values from sample.csv
            // Line 1: ad_id=1, bid=0.5
            // State: top=0.5, second=null, winner=1
            // Line 2: ad_id=2, bid=33
            // State: top=33, second=0.5, winner=2
            // Line 3: ad_id=3, bid=12
            // State: top=33, second=12, winner=2
             // Line 4: ad_id=4, bid=33.5
            // FINAL State: top=33.5, second=33, winner=4
        }

        fclose($fp);

        // Final Checks
        if ($count == 0) {
            throw new Exception("No valid entries found in the CSV file");
        }

        if ($count < 2) {
            throw new Exception("At least 2 valid entries are required for an auction");
        }

        // Return Result
        return [
            'ad_id' => $this->winner_id,
            'price' => $this->second_bid
        ];
    }


    // Format Output
    public static function format($result)
    {
        $price = $result['price'];

        // remove trailing zeros
        if (floor($price) == $price) { // Is it a whole number?
            $price_str = (string)(int)$price; // "33.0" → "33"
        } else {
            // Remove trailing zeros: "33.5000" → "33.5"
            $price_str = rtrim(rtrim(number_format($price, 10, '.', ''), '0'), '.');
        }

        return $result['ad_id'] . ', ' . $price_str;
    }
}

// Command-Line Entry Point
// run from CLI
if (php_sapi_name() === 'cli' && isset($argv) && realpath($argv[0]) === realpath(__FILE__)) {
    if ($argc != 2) {
        fwrite(STDERR, "Usage: php bid_auction.php <csv_file>\n");
        exit(1);
    }

    try {
        $auction = new BidAuction();
        $result = $auction->process($argv[1]);
        echo BidAuction::format($result) . "\n";
    } catch (Exception $e) {
        fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
        exit(1);
    }
}
