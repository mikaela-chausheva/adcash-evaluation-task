# Quick Notes for Interview

## What I did

BFS pathfinding with state tracking. Key idea: track (position, used_wall_break) not just position.

## Why BFS?

- BFS finds shortest path in unweighted graphs
- Explores level by level
- First time we reach goal = shortest path guaranteed

## The Tricky Part: State Tracking

Position (2,3) with wall break available ≠ Position (2,3) after using wall break

Same location, different future possibilities!

```
State = [row, col, used_break, distance]
used_break: 0 or 1
```

## Algorithm Flow

1. Start: queue = [(0,0,0,1)]
2. Pop from queue
3. Check if at goal → return distance
4. Try 4 directions:
   - If open (0): add with same used_break
   - If wall (1) AND used_break=0: break it, add with used_break=1
   - If wall (1) AND used_break=1: skip
5. Track visited to avoid loops
6. Repeat

## Complexity

**Time:** O(h × w)
- Each cell visited max 2 times (used_break: 0 and 1)
- Each visit: O(1)

**Space:** O(h × w)
- Queue + visited set

## Interview Questions I Should Be Ready For

**Q: Why not DFS?**
A: DFS goes deep first, might find long path before short one. BFS explores by distance.

**Q: Why track (pos, used_break)?**
A: Because being at (2,3) with ability available is different from being there after using it.

**Q: What if we could break K walls?**
A: Change state to track count of walls broken (0 to K). Complexity becomes O(h×w×k).

**Q: Time complexity?**
A: O(h×w). Each cell visited at most twice, O(1) per visit.

## Code Highlights

```php
// String keys for O(1) lookup
$visited["$row,$col,$used_break"] = true;

// SplQueue for O(1) enqueue/dequeue
$queue = new SplQueue();

// Main logic
if ($cell == 0) {
    // Can always go through open space
}
else if ($cell == 1 && $used_break == 0) {
    // Can break this wall
}
// else: already broke a wall, skip
```

## Testing

16 tests, all pass:
- Both provided examples
- Small cases (2x2, 3x3)
- Wall breaking scenarios
- Error handling

## Things to Remember

- BFS guarantees shortest in unweighted graphs
- State = position + ability status
- Each cell max 2 visits
- O(h×w) time and space
- SplQueue for efficiency

## If I Get Stuck

Draw small example (3×3) on paper and trace through BFS manually.
