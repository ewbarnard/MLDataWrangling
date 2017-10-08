<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 9:46 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class PowerOnSequence extends AbstractBase {
    /** @var StatementInterface */
    private $calculateQuery;

    /** @var StatementInterface */
    private $calculateUpdate;

    protected function main() {
        $this->prepareStatements();
        $this->walkSequence();
    }

    private function prepareStatements() {
        $sql = 'SELECT
	gpsmap64.calculate.id
FROM
	gpsmap64.trkpt
INNER JOIN gpsmap64.calculate ON gpsmap64.calculate.trkpt_id = gpsmap64.trkpt.id
WHERE
	trkpt.time >= ?
AND trkpt.time <= ?
AND calculate.power_sequence_id <> ?';
        $this->calculateQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.calculate SET power_sequence_id = ? WHERE id = ? LIMIT 1';
        $this->calculateUpdate = $this->connection->prepare($sql);
    }

    private function walkSequence() {
        $base = 0;
        $slice = 1000;
        $more = 1;
        while ($more) {
            $sql = "select id, time_begin, time_end from gpsmap64.power_sequence ORDER BY id LIMIT $base, $slice";
            $base += $slice;
            $query = $this->connection->query($sql);
            $rows = $query->fetchAll('assoc');
            if (is_array($rows) && count($rows)) {
                foreach ($rows as $row) {
                    $this->trackSequence($row);
                }
            } else {
                $more = 0;
            }
        }
    }

    private function trackSequence(array $psRow) {
        $psId = $psRow['id'];
        $this->calculateQuery->execute([$psRow['time_begin'], $psRow['time_end'], $psId]);
        $rows = $this->calculateQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                $this->calculateUpdate->execute([$psId, $row['id']]);
            }
        }
    }

}
