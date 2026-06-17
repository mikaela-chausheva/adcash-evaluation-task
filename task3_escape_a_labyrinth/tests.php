<?php
require_once 'solution.php';

$passed = 0;
$failed = 0;

function test($name, $map, $expected) {
    global $passed, $failed;

    try {
        $result = solution($map);
        if ($result === $expected) {
            echo "✓ $name: got $result\n";
            $passed++;
        } else {
            echo "✗ $name: expected $expected but got $result\n";
            $failed++;
        }
    } catch (Exception $e) {
        echo "✗ $name: threw exception: " . $e->getMessage() . "\n";
        $failed++;
    }
}

function testException($name, $map) {
    global $passed, $failed;

    try {
        $result = solution($map);
        echo "✗ $name: should have thrown exception but got $result\n";
        $failed++;
    } catch (Exception $e) {
        echo "✓ $name: correctly threw exception\n";
        $passed++;
    }
}

echo "Running tests...\n\n";

// Test the provided examples
test("Example 1 (6x6)", [
    [0, 0, 0, 0, 0, 0],
    [1, 1, 1, 1, 1, 0],
    [0, 0, 0, 0, 0, 0],
    [0, 1, 1, 1, 1, 1],
    [0, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0]
], 11);

test("Example 2 (4x4)", [
    [0, 1, 1, 0],
    [0, 0, 0, 1],
    [1, 1, 0, 0],
    [1, 1, 1, 0]
], 7);

// Simple cases
test("Simple 2x2 no walls", [
    [0, 0],
    [0, 0]
], 3);

test("2x2 with wall break", [
    [0, 1],
    [1, 0]
], 3);

test("3x3 straight path", [
    [0, 0, 0],
    [0, 0, 0],
    [0, 0, 0]
], 5);

// Cases that need wall breaking
test("Must break wall", [
    [0, 0, 1],
    [1, 0, 0],
    [0, 0, 0]
], 5);

test("Break wall strategically", [
    [0, 0, 1, 0],
    [1, 0, 1, 0],
    [0, 0, 1, 0],
    [0, 1, 1, 0]
], 7);

// Cases where wall break not needed
test("No break needed", [
    [0, 1, 0],
    [0, 1, 0],
    [0, 0, 0]
], 5);

// Larger mazes
test("5x5 complex", [
    [0, 1, 0, 0, 0],
    [0, 1, 0, 1, 0],
    [0, 0, 0, 1, 0],
    [1, 1, 1, 1, 0],
    [0, 0, 0, 0, 0]
], 9);

test("Rectangular 3x6", [
    [0, 0, 0, 0, 0, 0],
    [1, 1, 1, 1, 1, 0],
    [0, 0, 0, 0, 0, 0]
], 8);

// Edge cases
test("Mostly walls", [
    [0, 1, 0],
    [1, 1, 0],
    [1, 1, 0]
], 5);

test("Zigzag path", [
    [0, 0, 1, 0],
    [1, 0, 0, 0],
    [0, 0, 1, 0],
    [0, 1, 0, 0]
], 7);

// Error handling tests
echo "\nTesting error handling...\n";

testException("Empty map", []);

testException("Start is wall", [
    [1, 0],
    [0, 0]
]);

testException("End is wall", [
    [0, 0],
    [0, 1]
]);

testException("Map too small", [[0]]);

// Summary
echo "\n" . str_repeat("=", 40) . "\n";
echo "Results: $passed passed, $failed failed\n";

if ($failed === 0) {
    echo "All tests passed! ✓\n";
    exit(0);
} else {
    echo "Some tests failed\n";
    exit(1);
}
