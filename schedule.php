<?php

/**
 * Class for add urls to schedule
 */
class Schedule {
    
    /**
     * Add urls from file to queue.
     * @param string $fileName file for load
     * @return string user message
     */
    public static function save($fileName)
    {
        $urls = self::_loadFile($fileName);

        if ($urls) {
            $queue = new Queue('download');
            return $queue->addUrls($urls);
        }

        return Messages::FILE_NOT_FOUND;
    } 

    /**
     * Clear failed and done queue.
     * @return string user message
     */
    public static function clearResult()
    {
        $failedQueue = new Queue('failed');
        $doneQueue = new Queue('done');
        
        if ($failedQueue->clear() && $doneQueue->clear()) {
            return Messages::CLEARED;
        }

        return Messages::UNSPECIFIED_ERROR;
    }

    /**
     * Load data from file.
     * @param strng $fileName name of file for load
     * @return file|boolean
     */
    private static function _loadFile($fileName)
    {
        if (file_exists($fileName)) {
            return file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } else {
            return false;
        }
    }
}