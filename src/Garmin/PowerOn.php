<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 9:43 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class PowerOn extends AbstractBase {
    /** @var StatementInterface */
    private $psQuery;

    /** @var StatementInterface */
    private $psUpdate;

    /** @var StatementInterface */
    private $psInsert;

    private $psId = 0;

    private $psTimeBegin = '';

    private $psTimeEnd = '';

    private $epochStart = 0;

    private $epochNow = 0;

    private $priorTimeBegin = '';

    private $priorTimeEnd = '';

    protected function main() {
        $this->prepareStatements();
        $this->detectPowerOn();
    }

    private function prepareStatements() {
        $sql = 'SELECT id, time_begin, time_end FROM gpsmap64.power_sequence WHERE time_begin = ? LIMIT 1';
        $this->psQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.power_sequence SET time_end = ? WHERE id = ? LIMIT 1';
        $this->psUpdate = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.power_sequence (time_begin, time_end) VALUES (?, ?)';
        $this->psInsert = $this->connection->prepare($sql);
    }

    private function detectPowerOn() {
        $base = 0;
        $slice = 1000;
        $more = 1;
        while ($more) {
            $sql = "select time from gpsmap64.trkpt ORDER BY time LIMIT $base, $slice";
            $base += $slice;
            $query = $this->connection->query($sql);
            $rows = $query->fetchAll('assoc');
            if (is_array($rows) && count($rows)) {
                foreach ($rows as $row) {
                    $this->trackPowerOn($row);
                }
            } else {
                $more = 0;
            }
        }
        $this->flushLastSequence();
    }

    private function trackPowerOn(array $row) {
        $now = (string)$row['time'];
        $this->epochNow = strtotime($now);
        $elapsed = $this->epochNow - $this->epochStart;
        if ($elapsed > 600) {
            // New sequence detected
            $this->flushLastSequence();
            $this->priorTimeBegin = $now;
        }
        $this->epochStart = $this->epochNow;
        $this->priorTimeEnd = $now;
    }

    private function flushLastSequence() {
        if ($this->priorTimeBegin === '') {
            return;
        }
        $this->lookupPs($this->priorTimeBegin);
        if ($this->psId && ($this->priorTimeEnd === $this->psTimeEnd)) {
            return;
        }
        if ($this->psId) {
            $this->psUpdate->execute([$this->priorTimeEnd, $this->psId]);
        } else {
            $this->psInsert->execute([$this->priorTimeBegin, $this->priorTimeEnd]);
        }
    }

    private function lookupPs($timeBegin) {
        $this->psId = 0;
        $this->psTimeBegin = '';
        $this->psTimeEnd = '';
        $this->psQuery->execute([$timeBegin]);
        $row = $this->psQuery->fetch('assoc');
        if (is_array($row) && array_key_exists('id', $row)) {
            $this->psId = (int)$row['id'];
            $this->psTimeBegin = (string)$row['time_begin'];
            $this->psTimeEnd = (string)$row['time_end'];
        }
    }

}
