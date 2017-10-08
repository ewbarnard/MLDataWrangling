<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 9:50 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class TrackSpeed extends AbstractBase {
    /** @var StatementInterface */
    private $calculateQuery;

    /** @var StatementInterface */
    private $calculateUpdate;

    /** @var StatementInterface */
    private $trkptQuery;

    private $priorRow = [];

    private $epochStart = 0;

    protected function main() {
        $this->prepareStatements();
        $this->loopSequence();
    }

    private function loopSequence() {
        $this->calculateQuery->execute([]);
        $rows = $this->calculateQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                $this->singleSequence((int)$row['power_sequence_id']);
            }
        }
    }

    private function singleSequence($psId) {
        $first = true;
        $this->trkptQuery->execute([$psId]);
        $rows = $this->trkptQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                $this->trackSpeed($row, $first);
                $first = false;
            }
        }
    }

    private function trackSpeed(array $row, $first) {
        if ($first) {
            $this->epochStart = strtotime($row['time']);
            $parms = [
                0, 0, 0, 0, 0, 1, $row['id'],
            ];
        } else {
            $this->calculateSpeed($this->priorRow, $row);
            $elapsed = strtotime($row['time']) - $this->epochStart;
            $parms = [
                $this->feet, $this->seconds, sprintf('%.2f', $this->mph),
                sprintf('%.4f', $this->climb), $elapsed, 0, $row['id'],
            ];
        }
        $this->calculateUpdate->execute($parms);
        $this->priorRow = $row;
    }

    private function prepareStatements() {
        $sql = 'SELECT DISTINCT power_sequence_id FROM gpsmap64.calculate WHERE has_speed = 0';
        $this->calculateQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.calculate SET feet = ?, seconds = ?, mph = ?, climb = ?, 
          power_on_seconds = ?, is_sequence_start = ?, has_speed = 1 WHERE id = ? LIMIT 1';
        $this->calculateUpdate = $this->connection->prepare($sql);

        $sql = 'SELECT
	lat ,
	lon ,
	ele ,
	time ,
	gpsmap64.calculate.id
FROM
	gpsmap64.calculate
INNER JOIN gpsmap64.trkpt ON gpsmap64.trkpt.id = gpsmap64.calculate.trkpt_id
WHERE
	power_sequence_id = ?
ORDER BY time 
LIMIT 10000';
        $this->trkptQuery = $this->connection->prepare($sql);
    }

}
