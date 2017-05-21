<?php

require_once 'messages.php';
require_once 'schedule.php';
require_once 'queue.php';
require_once 'download.php';

if ($argc == 1) {
    die(Messages::NO_ARGUMENTS);
} else {
    try {
        switch ($argv[1]) {
            case 'schedule' : {
                $result = Schedule::save($argv[2]);
                break;
            }
            case 'download' : {
                $result = Download::process();
                break;
            }
            case 'clear_result' : {
                $result = Schedule::clearResult();
                break;
            }
            default : {   
                $result = Messages::NO_ARGUMENTS;
                break;
            }
        }

        echo $result;
    } catch (Exception $ex) {
        echo Messages::UNSPECIFIED_ERROR;
    }
}