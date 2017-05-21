<?php

/**
 * Class for downloading images.
 */
class Download
{

    /**
     * Main method for process urls. Download images by urls from queue.
     * @return string user messages.
     */
    public static function process()
    {
        $forLoad = new Queue('download');
        $urlsForLoad = $forLoad->getUrls();
        $totalForLoad = count($urlsForLoad);
        $processed = [
            'done' => [],
            'failed' => []
        ];

        $process = 1;
        foreach ($urlsForLoad as $url) {
            $url = $url[Queue::FIELD_NAME];
            echo $process . '/' . $totalForLoad . PHP_EOL;
            if (self::_download($url)) {
                $processed['done'][] = $url;
            } else {
                $processed['failed'][] = $url;
            }
            $process++;
        }

        foreach($processed as $result => $urls) {
            $queue = new Queue($result);
            $queue->addUrls($urls);
        }
        
        $forLoad->clear();

        return $totalForLoad . ' '. Messages::PROCESSED . ': ' 
            . count($processed['done']) . ' ' . Messages::DOWNLOAD_DONE . ', '
            . count($processed['failed']) . ' ' . Messages::DOWNLOAD_FAILED . '.';
    }
    
    
    /**
     * Download images by urls from queue.
     * @param string $url Image url for download
     * @return bool download result
     */
    private static function _download($url)
    {
        $fileName = self::_getFileName(basename($url));

        $imageInfo = @getimagesize($url);

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                return imagejpeg(imagecreatefromjpeg($url), $fileName);
            case IMAGETYPE_PNG:
                return imagepng(imagecreatefrompng($url), $fileName);
            case IMAGETYPE_GIF:
                return imagegif(imagecreatefromgif($url), $fileName);
        }

        return false;
    }
    
    /**
     * Get filename for new file.
     * Make a new filename in format "oldname_time.ext" if file already exist.
     * @param string $name filename
     * @param string $defaultName default filename (for renaming)
     * @return string new filename (with path)
     */
    private static function _getFileName($name, $defaultName = null) {
        $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name;

        if (file_exists($fileName)) {
            if ($defaultName) {
                $name = $defaultName;
            }
            $mk = explode('.', microtime(true));
            $file = explode('.', $name);
            $extention = $file[count($file) - 1];
            $newName = substr($name, 0, (strlen($extention) + 1) * -1) . '_' 
                . date('Y-m-d H.i.s.') . $mk[1] . '.' . $extention;
            return self::_getFileName($newName, $defaultName);
        }

        return $fileName;
    }
}