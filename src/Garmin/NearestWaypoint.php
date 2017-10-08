<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 9:40 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class NearestWaypoint extends AbstractBase {
    /** @var StatementInterface */
    private $distanceQuery;

    /** @var StatementInterface */
    private $calculateQuery;

    /** @var StatementInterface */
    private $calculateInsert;

    /** @var StatementInterface */
    private $calculateUpdate;

    private $wptIdNearest = 0;

    private $wptIdCurrent = 0;

    private $calculateId = 0;

    protected function main() {
        $this->prepareStatements();
        $this->walkTracks();
    }

    private function prepareStatements() {
        $sql = 'SELECT wpt_id FROM gpsmap64.distance WHERE trkpt_id = ? ORDER BY feet LIMIT 1';
        $this->distanceQuery = $this->connection->prepare($sql);

        $sql = 'SELECT id, wpt_id FROM gpsmap64.calculate WHERE trkpt_id = ? LIMIT 1';
        $this->calculateQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.calculate SET wpt_id = ? WHERE id = ? LIMIT 1';
        $this->calculateUpdate = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.calculate (trkpt_id, wpt_id) VALUES (?, ?)';
        $this->calculateInsert = $this->connection->prepare($sql);
    }

    private function walkTracks() {
        $base = 0;
        $slice = 1000;
        $more = 1;
        while ($more) {
            $sql = "select id from gpsmap64.trkpt ORDER BY id LIMIT $base, $slice";
            $base += $slice;
            $query = $this->connection->query($sql);
            $rows = $query->fetchAll('assoc');
            if (is_array($rows) && count($rows)) {
                foreach ($rows as $row) {
                    $this->nearest((int)$row['id']);
                }
            } else {
                $more = 0;
            }
        }
    }

    private function nearest($trkptId) {
        $this->lookupNearest($trkptId);
        $this->lookupCurrent($trkptId);
        if ($this->calculateId) {
            if ($this->wptIdCurrent !== $this->wptIdNearest) {
                $this->calculateUpdate->execute([$this->wptIdNearest, $this->calculateId]);
            }
        } else {
            $this->calculateInsert->execute([$trkptId, $this->wptIdNearest]);
        }
    }

    private function lookupNearest($trkptId) {
        $this->distanceQuery->execute([$trkptId]);
        $row = $this->distanceQuery->fetch('assoc');
        $this->wptIdNearest = (is_array($row) && array_key_exists('wpt_id', $row)) ? (int)$row['wpt_id'] : 0;
    }

    private function lookupCurrent($trkptId) {
        $this->wptIdCurrent = 0;
        $this->calculateId = 0;
        $this->calculateQuery->execute([$trkptId]);
        $row = $this->calculateQuery->fetch('assoc');
        if (is_array($row) && array_key_exists('id', $row)) {
            $this->calculateId = (int)$row['id'];
            $this->wptIdCurrent = (int)$row['wpt_id'];
        }
    }

}
