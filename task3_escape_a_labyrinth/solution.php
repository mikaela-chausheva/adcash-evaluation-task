<?php
/**
 * Labyrinth pathfinding with one wall break allowed
 * Using BFS because we need the shortest path
 */

function solution($map) {
    // Basic validation
    if (empty($map) || !is_array($map)) {
        throw new InvalidArgumentException("Map cannot be empty");
    }

    $height = count($map);
    $width = count($map[0]);

    // Check dimensions
    if ($height < 2 || $height > 20 || $width < 2 || $width > 20) {
        throw new InvalidArgumentException("Map dimensions must be between 2 and 20");
    }

    // Make sure start and end are valid
    if ($map[0][0] !== 0) {
        throw new InvalidArgumentException("Starting position must be passable");
    }
    if ($map[$height-1][$width-1] !== 0) {
        throw new InvalidArgumentException("Ending position must be passable");
    }

    // BFS setup
    // Each state is [row, col, used_wall_break, distance]
    $queue = new SplQueue();
    $queue->enqueue([0, 0, 0, 1]);

    // Track visited states - key format: "row,col,used_break"
    $visited = [];
    $visited["0,0,0"] = true;

    // Four directions: right, down, left, up
    $dirs = [[0,1], [1,0], [0,-1], [-1,0]];

    while (!$queue->isEmpty()) {
        list($r, $c, $used_break, $dist) = $queue->dequeue();

        // Found the exit?
        if ($r == $height - 1 && $c == $width - 1) {
            return $dist;
        }

        // Try all 4 directions
        foreach ($dirs as $d) {
            $nr = $r + $d[0];
            $nc = $c + $d[1];

            // Check bounds
            if ($nr < 0 || $nr >= $height || $nc < 0 || $nc >= $width) {
                continue;
            }

            $cell = $map[$nr][$nc];

            if ($cell == 0) {
                // Open space - can always move here
                $key = "$nr,$nc,$used_break";
                if (!isset($visited[$key])) {
                    $visited[$key] = true;
                    $queue->enqueue([$nr, $nc, $used_break, $dist + 1]);
                }
            } else if ($cell == 1 && $used_break == 0) {
                // Wall, but we can break it since we haven't used our break yet
                $key = "$nr,$nc,1";
                if (!isset($visited[$key])) {
                    $visited[$key] = true;
                    $queue->enqueue([$nr, $nc, 1, $dist + 1]);
                }
            }
            // If it's a wall and we already broke one, just skip
        }
    }

    return -1; // No path found (shouldn't happen per problem statement)
}

// Quick test
if (php_sapi_name() === 'cli') {
    $test1 = [
        [0, 0, 0, 0, 0, 0],
        [1, 1, 1, 1, 1, 0],
        [0, 0, 0, 0, 0, 0],
        [0, 1, 1, 1, 1, 1],
        [0, 1, 1, 1, 1, 1],
        [0, 0, 0, 0, 0, 0]
    ];

    $test2 = [
        [0, 1, 1, 0],
        [0, 0, 0, 1],
        [1, 1, 0, 0],
        [1, 1, 1, 0]
    ];

    echo "Test 1: " . solution($test1) . " (expected 11)\n";
    echo "Test 2: " . solution($test2) . " (expected 7)\n";
}
