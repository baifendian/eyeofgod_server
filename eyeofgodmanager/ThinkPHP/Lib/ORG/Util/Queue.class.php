<?php
/*
Queue Class
By Matthew McConnell 2006

Version 1a - 1st May 2006
SQL:

CREATE TABLE `queue` (
`id` BIGINT NOT NULL AUTO_INCREMENT ,
`user` VARCHAR( 255 ) NOT NULL ,
`time` INT NOT NULL ,
`qid` INT NOT NULL ,
PRIMARY KEY ( `id` )
);

*/

class Queue
{
    var $queue_table = "queue"; // Location of the queue table
    var $timeout = 2; // Timeout (seconds) so that the queue doesn't stall
    var $user;
    var $qid;
    var $qTime;

    function Queue($qid)
    {
        $this->user = md5(uniqid(rand(), true));
        $this->qid = $qid;
        $this->cleanQueue();
    }

    function setQueueTable($t)
    {
        $this->queue_table = $t;
    }

    function setTimeout($t)
    {
        $this->timeout = $t;
    }

    function getInQueue()
    {
        $now = time();
        mysql_query("INSERT INTO `{$this->queue_table}` (`user`, `time`, `qid`) VALUES ('{$this->user}', {$now}, {$this->qid})");
    }

    function getOutOfQueue()
    {
        mysql_query("DELETE FROM `{$this->queue_table}` WHERE `user` = '{$this->user}'");
    }

    function getFirstInLine()
    {
        $q = mysql_fetch_assoc(mysql_query("SELECT * FROM `{$this->queue_table}` WHERE `qid` = {$this->qid} ORDER BY `id` ASC"));
        return $q['user'];
    }

    function cleanQueue()
    {
        $timeout = time() - $this->timeout;
        mysql_query("DELETE FROM `{$this->queue_table}` WHERE `time` < {$timeout}");
    }

    function updateTime()
    {
        $now = time();
        mysql_query("UPDATE `{$this->queue_table}` SET `time` = {$now} WHERE `user` = '{$this->user}' LIMIT 1");
    }

    function queueTime()
    {
        return $this->qTime;
    }

    function isInQueue()
    {
        $q = mysql_num_rows(mysql_query("SELECT * FROM `{$this->queue_table}` WHERE `user` = '{$this->user}'"));
        if ($q < 1) return false;
        if ($q > 0) return true;
    }

    function waitInLine()
    {
        $calibrate_begin = microtime_float();
        $calibrate_end = microtime_float();
        $overhead_time = $calibrate_end - $calibrate_begin;
        $performance_begin = microtime_float();
        $first = $this->getFirstInLine();
        $this->updateTime();
        unset($return);
        if ($first != $this->user)
        {
            while ($first != $this->user)
            {
                $this->updateTime($this->user);
                $this->cleanQueue();
                $first = $this->getFirstInLine();
                if ($this->isInQueue() == false)
                {
                    $first = $this->user;
                    $return = false;
                }
            }
        }
        if (!isset($return)) $return = true;
        if ($return == false) $this->getOutOfQueue();
        $performance_end = microtime_float();
        $result_time = round(($performance_end - $performance_begin) - $overhead_time, 4);
        $this->qTime = $result_time;
        return $return;
    }
}
?> 