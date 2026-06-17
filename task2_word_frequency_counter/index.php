<?php

require_once 'WordFrequencyCounter.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$counter = new WordFrequencyCounter();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = [];
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '', $query);

function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function error_response($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_PRETTY_PRINT);
    exit;
}

try {
    if ($method === 'POST' && $path === '/texts') {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            error_response('Empty request body');
        }

        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_response('Invalid JSON: ' . json_last_error_msg());
        }

        if (!isset($data['text'])) {
            error_response('Missing "text" field');
        }

        $text = $data['text'];

        if (!is_string($text) || empty(trim($text))) {
            error_response('Text must be non-empty string');
        }

        $counter->addText($text);

        // Original implementation - only returned success message
        // This follows RESTful best practice where POST confirms creation
        // and GET is used separately to retrieve data
        // json_response([
        //     'message' => 'Text processed successfully',
        //     'text_length' => strlen($text)
        // ], 201);

        // Updated implementation - returns cumulative word frequencies immediately
        // This matches the requirement to show "State after the execution"
        // Allows user to see results without making a separate GET request
        $freq = $counter->getAllFrequencies();
        json_response([
            'message' => 'Text processed successfully',
            'text_length' => strlen($text),
            'total_unique_words' => count($freq),
            'frequencies' => $freq
        ], 201);

    } elseif ($method === 'GET' && $path === '/words') {
        $freq = $counter->getAllFrequencies();

        json_response([
            'total_unique_words' => count($freq),
            'frequencies' => $freq
        ]);

    } elseif ($method === 'GET' && $path === '/words/search') {
        if (!isset($query['word']) || empty($query['word'])) {
            error_response('Missing "word" parameter');
        }

        $word = $query['word'];
        $count = $counter->getWordFrequency($word);

        json_response([
            'word' => strtolower(trim($word)),
            'frequency' => $count
        ]);

    } elseif ($method === 'GET' && $path === '/health') {
        json_response([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

    } else {
        error_response('Not found', 404);
    }

} catch (InvalidArgumentException $e) {
    error_response($e->getMessage());
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    error_response('Internal server error: ' . $e->getMessage(), 500);
}
