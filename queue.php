<?php

/**
 * Class for work with sqlite queue.
 */
class Queue
{
    const FIELD_NAME = 'entity';
    
    private $db;
    private $queueName;
    private $dbName = 'botQueue.sqlite';
    private $fieldType = 'TEXT NOT NULL';

    /**
     * Check and create table for queue if needed;
     * @param string $queueName Name for queue table.
     */
    public function __construct($queueName)
    {
        $this->db = new PDO('sqlite:' . sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->dbName);;
        $this->queueName = trim(strip_tags($queueName));
        $this->db->exec('CREATE TABLE IF NOT EXISTS ' . $this->queueName 
            . ' (' . self::FIELD_NAME . ' ' . $this->fieldType . ')');
    }

    /**
     * Add urls array to queue.
     * @param array $values Array os string urls.
     * @return string user message
     */
    public function addUrls(array $values)
    {
        $insertValues = [];
        
        foreach ($values as $value) {
            if (!empty($value)) {
                $insertValues[] = '(\'' . trim(strip_tags($value)) . '\')';
            }
        }

        $result = $this->db->exec('INSERT INTO ' . $this->queueName 
            . ' (' . self::FIELD_NAME . ') VALUES ' . implode(',', $insertValues) );

        if ($result) {
            return $result . Messages::ADDED_TO_QUEUE;
        }
        
        return Messages::UNSPECIFIED_ERROR;
    }

    /**
     * Get urls from queue.
     * @return array of urls
     */
    public function getUrls()
    {
        return $this->db->query('SELECT * FROM ' . $this->queueName)->fetchAll();
    }

    /**
     * Clear queue.
     * @return int
     */
    public function clear() {
        return $this->db->exec('DELETE FROM ' . $this->queueName);
    }
}