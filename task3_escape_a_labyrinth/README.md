# Labyrinth Escape - Task 3

Finding the shortest path through a maze where you can break one wall.

## The Problem

You have a maze represented as a 2D grid:
- `0` = open space (can walk through)
- `1` = wall (can't walk through)

**Goal:** Find shortest path from top-left (0,0) to bottom-right, with ability to break **one wall**.

**Rules:**
- Only move in 4 directions (no diagonals)
- Path length = number of cells visited (including start and end)
- Start and end are always passable
- Map size: 2x2 to 20x20

## How to Run

```bash
# Run with test examples
php solution.php

# Run full test suite
php tests.php
```

## Examples

### Example 1:
```
Input:
[0, 0, 0, 0, 0, 0]
[1, 1, 1, 1, 1, 0]
[0, 0, 0, 0, 0, 0]
[0, 1, 1, 1, 1, 1]
[0, 1, 1, 1, 1, 1]
[0, 0, 0, 0, 0, 0]

Output: 11
```

### Example 2:
```
Input:
[0, 1, 1, 0]
[0, 0, 0, 1]
[1, 1, 0, 0]
[1, 1, 1, 0]

Output: 7
```

## Algorithm

Using **BFS (Breadth-First Search)** with state tracking.

### Why BFS?
BFS explores paths level by level, so the first time we reach the destination, we know it's the shortest path. DFS would go deep first and might miss shorter paths.

### The Key Insight: State Tracking

This is the important part - we track states as `(row, col, used_wall_break)` not just `(row, col)`.

**Why?** Because position (2,3) where you haven't broken a wall yet is different from position (2,3) where you already broke a wall. In the first case, you can still break a wall ahead!

```php
State = [row, column, used_wall_break, distance]

// used_wall_break is:
// 0 = haven't used it yet
// 1 = already broke a wall
```

### How It Works

1. Start at (0,0) with `used_wall_break = 0`
2. For each position, try moving in 4 directions
3. If next cell is passable (0): move there with same `used_wall_break` value
4. If next cell is wall (1) AND we haven't broken a wall yet: break it and move there with `used_wall_break = 1`
5. If it's a wall and we already broke one: can't go there
6. Keep track of visited states to avoid loops
7. First time we reach the end = shortest path

### Complexity

- **Time:** O(height × width) - each cell visited at most twice (once with wall break available, once without)
- **Space:** O(height × width) - for the queue and visited tracking

## Code Structure

```php
function solution($map) {
    // 1. Validate input
    // 2. Setup BFS (queue, visited set)
    // 3. Main BFS loop
    //    - Check if at goal
    //    - Try all 4 directions
    //    - Add valid moves to queue
    // 4. Return shortest distance
}
```

## Testing

The test file includes:
- Both provided examples ✓
- Simple cases (2x2, 3x3)
- Cases requiring wall breaks
- Cases not requiring wall breaks
- Larger mazes
- Error handling (invalid inputs)

All tests pass.

## Usage Example

```php
require_once 'solution.php';

$maze = [
    [0, 0, 0, 0, 0, 0],
    [1, 1, 1, 1, 1, 0],
    [0, 0, 0, 0, 0, 0],
    [0, 1, 1, 1, 1, 1],
    [0, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0]
];

$result = solution($maze);
echo $result; // Output: 11
```

## Understanding the Solution

If you need to explain this in an interview:

1. **Why BFS?** "BFS guarantees shortest path in unweighted graphs because it explores by distance"

2. **Why state tracking?** "We need to know if we've used our wall-breaking ability. Same position but different ability status = different state"

3. **Complexity?** "O(h×w) because each cell is visited at most twice, and each visit is O(1)"

4. **What if K walls?** "Change state to track number of walls broken instead of just 0/1. Complexity becomes O(h×w×k)"

## Notes

- PHP 7.0+, no external dependencies
- Uses SplQueue for O(1) enqueue/dequeue
- String keys for visited states ("row,col,used_break") for fast lookup
