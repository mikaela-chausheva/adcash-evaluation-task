<?php

class WordFrequencyCounter
{
    private $storageFile;
    private $frequencies = [];

    public function __construct($storageFile = 'word_frequencies.json')
    {
        $this->storageFile = $storageFile;
        $this->loadFromFile();
    }

    private function loadFromFile()
    {
        if (!file_exists($this->storageFile)) {
            $this->frequencies = [];
            return;
        }

        $fp = fopen($this->storageFile, 'r');
        if (!$fp) {
            error_log("Could not open file for reading");
            $this->frequencies = [];
            return;
        }

        if (flock($fp, LOCK_SH)) {
            $content = stream_get_contents($fp);
            flock($fp, LOCK_UN);

            if ($content) {
                $data = json_decode($content, true);
                $this->frequencies = $data ? $data : [];
            }
        }

        fclose($fp);
    }

    private function saveToFile()
    {
        $fp = fopen($this->storageFile, 'c');
        if (!$fp) {
            error_log("Could not open file for writing");
            return;
        }

        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($this->frequencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }

    public function addText($text)
    {
        if (empty(trim($text))) {
            throw new InvalidArgumentException("Text must be a non-empty string");
        }

        $this->loadFromFile();

        // extract words and convert to lowercase
        preg_match_all('/\b[a-zA-Z]+\b/', strtolower($text), $matches);
        $words = $matches[0];

        foreach ($words as $word) {
            if (!isset($this->frequencies[$word])) {
                $this->frequencies[$word] = 0;
            }
            $this->frequencies[$word]++;
        }

        $this->saveToFile();
    }

    public function getAllFrequencies()
    {
        $this->loadFromFile();
        return $this->frequencies;
    }

    public function getWordFrequency($word)
    {
        if (empty(trim($word))) {
            throw new InvalidArgumentException("Word must be a non-empty string");
        }

        $this->loadFromFile();
        $word = strtolower(trim($word));

        return isset($this->frequencies[$word]) ? $this->frequencies[$word] : 0;
    }

    public function clear()
    {
        $this->frequencies = [];
        $this->saveToFile();
    }

    public function getTotalUniqueWords()
    {
        $this->loadFromFile();
        return count($this->frequencies);
    }
}
