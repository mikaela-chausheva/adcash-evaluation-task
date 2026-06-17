<?php

require_once 'WordFrequencyCounter.php';

$testFile = 'test_word_frequencies.json';
$passed = 0;
$failed = 0;
$total = 0;

function pass($msg) {
    global $passed, $total;
    $total++;
    $passed++;
    echo "✓ $msg\n";
}

function fail($msg, $expected = null, $actual = null) {
    global $failed, $total;
    $total++;
    $failed++;
    echo "✗ $msg\n";
    if ($expected !== null) {
        echo "  Expected: " . var_export($expected, true) . "\n";
        echo "  Actual: " . var_export($actual, true) . "\n";
    }
}

function assert_equals($expected, $actual, $msg) {
    if ($expected === $actual) {
        pass($msg);
    } else {
        fail($msg, $expected, $actual);
    }
}

function cleanup($file) {
    if (file_exists($file)) unlink($file);
}

echo "===========================================\n";
echo "Running tests...\n";
echo "===========================================\n\n";

// Test 1: Basic counting
echo "Test 1: Basic word counting\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("Love grows where kindness lives.");
$freq = $counter->getAllFrequencies();
assert_equals(1, $freq['love'] ?? 0, "'love' appears 1 time");
assert_equals(1, $freq['grows'] ?? 0, "'grows' appears 1 time");
assert_equals(5, count($freq), "5 unique words");
cleanup($testFile);
echo "\n";

// Test 2: Multiple texts
echo "Test 2: Multiple text submissions\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("Love grows where kindness lives.");
$counter->addText("Kindness lives in every heart.");
$freq = $counter->getAllFrequencies();
assert_equals(2, $freq['kindness'] ?? 0, "'kindness' appears 2 times");
assert_equals(2, $freq['lives'] ?? 0, "'lives' appears 2 times");
assert_equals(8, count($freq), "8 unique words total");
cleanup($testFile);
echo "\n";

// Test 3: Case insensitive
echo "Test 3: Case insensitivity\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("Love LOVE love LoVe");
$freq = $counter->getAllFrequencies();
assert_equals(4, $freq['love'] ?? 0, "'love' counted case-insensitive");
assert_equals(1, count($freq), "Only 1 unique word");
cleanup($testFile);
echo "\n";

// Test 4: Punctuation
echo "Test 4: Punctuation handling\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("Hello, world! Hello? World... Hello!");
$freq = $counter->getAllFrequencies();
assert_equals(3, $freq['hello'] ?? 0, "'hello' appears 3 times");
assert_equals(2, $freq['world'] ?? 0, "'world' appears 2 times");
cleanup($testFile);
echo "\n";

// Test 5: Get word frequency
echo "Test 5: Get specific word\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("Love grows where kindness lives.");
$counter->addText("Kindness lives in every heart.");
assert_equals(2, $counter->getWordFrequency('kindness'), "getWordFrequency works");
assert_equals(2, $counter->getWordFrequency('KINDNESS'), "case insensitive search");
assert_equals(0, $counter->getWordFrequency('missing'), "missing word returns 0");
cleanup($testFile);
echo "\n";

// Test 6: Persistence
echo "Test 6: Data persistence\n";
cleanup($testFile);
$c1 = new WordFrequencyCounter($testFile);
$c1->addText("test word");
unset($c1);
$c2 = new WordFrequencyCounter($testFile);
$freq = $c2->getAllFrequencies();
assert_equals(1, $freq['test'] ?? 0, "data persists");
assert_equals(1, $freq['word'] ?? 0, "data persists");
cleanup($testFile);
echo "\n";

// Test 7: Concurrent access
echo "Test 7: Concurrent access\n";
cleanup($testFile);
$c1 = new WordFrequencyCounter($testFile);
$c1->addText("apple banana");
$c2 = new WordFrequencyCounter($testFile);
$c2->addText("banana cherry");
$c1 = new WordFrequencyCounter($testFile);
$freq = $c1->getAllFrequencies();
assert_equals(1, $freq['apple'] ?? 0, "1 apple");
assert_equals(2, $freq['banana'] ?? 0, "2 bananas");
assert_equals(1, $freq['cherry'] ?? 0, "1 cherry");
cleanup($testFile);
echo "\n";

// Test 8: Empty validation
echo "Test 8: Empty text validation\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$threw = false;
try {
    $counter->addText("");
} catch (InvalidArgumentException $e) {
    $threw = true;
}
if ($threw) {
    pass("throws exception for empty text");
} else {
    fail("should throw exception for empty text");
}
cleanup($testFile);
echo "\n";

// Test 9: Large text
echo "Test 9: Large text\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$text = str_repeat("word ", 1000) . str_repeat("another ", 500);
$counter->addText($text);
$freq = $counter->getAllFrequencies();
assert_equals(1000, $freq['word'] ?? 0, "handles 1000 words");
assert_equals(500, $freq['another'] ?? 0, "handles 500 words");
cleanup($testFile);
echo "\n";

// Test 10: Special chars
echo "Test 10: Special characters\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("test123 hello @world #hashtag");
$freq = $counter->getAllFrequencies();
assert_equals(1, $freq['hello'] ?? 0, "extracts 'hello'");
assert_equals(1, $freq['world'] ?? 0, "extracts 'world'");
assert_equals(1, $freq['hashtag'] ?? 0, "extracts 'hashtag'");
if (!isset($freq['test123'])) {
    pass("ignores alphanumeric");
} else {
    fail("should ignore alphanumeric");
}
cleanup($testFile);
echo "\n";

// Test 11: Clear
echo "Test 11: Clear\n";
cleanup($testFile);
$counter = new WordFrequencyCounter($testFile);
$counter->addText("some words here");
$counter->clear();
$freq = $counter->getAllFrequencies();
assert_equals(0, count($freq), "clear works");
cleanup($testFile);
echo "\n";

echo "===========================================\n";
echo "Results\n";
echo "===========================================\n";
echo "Total: $total\n";
echo "Passed: $passed ✓\n";
echo "Failed: $failed ✗\n";
if ($total > 0) {
    echo "Success: " . round(($passed / $total) * 100, 2) . "%\n";
}
echo "===========================================\n";

exit($failed > 0 ? 1 : 0);
