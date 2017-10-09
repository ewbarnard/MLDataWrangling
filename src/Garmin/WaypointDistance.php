<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 8:47 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class WaypointDistance extends AbstractBase {
    /** @var StatementInterface */
    private $wptQuery;

    /** @var StatementInterface */
    private $distanceQuery;

    /** @var StatementInterface */
    private $distanceInsert;

    /** @var StatementInterface */
    private $distanceCount;

    private $waypoints = [];

    private $waypointCount = 0;

    private $distanceId = 0;

    protected function main() {
        $this->prepareStatements();
        $this->loadWaypoints();
        $this->trackDistance();
    }

    private function prepareStatements() {
        $sql = 'SELECT id, lat, lon FROM gpsmap64.wpt ORDER BY id LIMIT 20000';
        $this->wptQuery = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.distance WHERE wpt_id = ? AND trkpt_id = ? LIMIT 1';
        $this->distanceQuery = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.distance (wpt_id, trkpt_id, feet) VALUES (?, ?, ?)';
        $this->distanceInsert = $this->connection->prepare($sql);

        $sql = 'SELECT count(*) `count` FROM gpsmap64.distance WHERE trkpt_id = ? LIMIT 1';
        $this->distanceCount = $this->connection->prepare($sql);
    }

    private function loadWaypoints() {
        $this->wptQuery->execute([]);
        $rows = $this->wptQuery->fetchAll('assoc');
        foreach ($rows as $row) {
            $id = (int)$row['id'];
            $lat = (float)$row['lat'];
            $lon = (float)$row['lon'];
            $this->waypoints[$id] = ['lat' => $lat, 'lon' => $lon];
        }
        $this->waypointCount = count($this->waypoints);
    }

    private function trackDistance() {
        $base = 0;
        $slice = 1000;
        $more = 1;
        while ($more) {
            $sql = "SELECT id, lat, lon FROM gpsmap64.trkpt ORDER BY id LIMIT $base, $slice";
            $base += $slice;
            $query = $this->connection->query($sql);
            $rows = $query->fetchAll('assoc');
            if (is_array($rows) && count($rows)) {
                foreach ($rows as $row) {
                    $this->trackDistanceRow($row);
                }
            } else {
                $more = 0;
            }
        }
    }

    private function trackDistanceRow(array $row) {
        $trkptId = $row['id'];
        if ($this->waypointCount !== $this->countDistance($trkptId)) {
            foreach ($this->waypoints as $wptId => $waypoint) {
                $this->lookupDistance($wptId, $trkptId);
                if (!$this->distanceId) {
                    $this->calculateDistance($waypoint, $row);
                    $feet = round($this->feet);
                    $this->distanceInsert->execute([$wptId, $trkptId, $feet]);
                }
            }
        }
    }

    private function countDistance($trkptId) {
        $this->distanceCount->execute([$trkptId]);
        $row = $this->distanceCount->fetch('assoc');
        return (is_array($row) && array_key_exists('count', $row)) ? (int)$row['count'] : 0;
    }

    private function lookupDistance($wptId, $trkptId) {
        $this->distanceId = 0;
        $this->distanceQuery->execute([$wptId, $trkptId]);
        $row = $this->distanceQuery->fetch('assoc');
        if (is_array($row) && array_key_exists('id', $row)) {
            $this->distanceId = (int)$row['id'];
        }
    }
}
