<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 8:21 AM
 */

namespace App\Garmin;

use Cake\Console\Exception\StopException;
use Cake\Database\StatementInterface;

class ImportGarmin extends AbstractBase {
    /** @var StatementInterface */
    private $wptInsert;

    /** @var StatementInterface */
    private $wptUpdate;

    /** @var StatementInterface */
    private $wptUnique;

    /** @var StatementInterface */
    private $trkInsert;

    /** @var StatementInterface */
    private $trkQuery;

    /** @var StatementInterface */
    private $trksegInsert;

    /** @var StatementInterface */
    private $trksegQuery;

    /** @var StatementInterface */
    private $trkptInsert;

    /** @var StatementInterface */
    private $trkptQuery;

    private $section = [];

    private $trksegMap = [];

    protected function main() {
        $this->prepareStatements();
        $this->importGarminExports();
    }

    private function prepareStatements() {
        $sql = 'INSERT INTO gpsmap64.wpt 
          (wpt_csv_id, lat, lon, ele, time, name, cmt, `desc`) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $this->wptInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.wpt WHERE lat = ? AND lon = ? AND ele = ? LIMIT 1';
        $this->wptUnique = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.wpt SET wpt_csv_id = ?, name = ?, cmt = ?, `desc` = ? WHERE id = ? LIMIT 1';
        $this->wptUpdate = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.trk
          (file_id, trk_csv_id, name, cmt, `desc`) VALUES (?, ?, ?, ?, ?)';
        $this->trkInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.trk WHERE file_id = ? AND trk_csv_id = ? LIMIT 1';
        $this->trkQuery = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.trkseg
          (trk_id, trkseg_csv_id, trk_csv_id) VALUES (?, ?, ?)';
        $this->trksegInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.trkseg WHERE trk_id = ? AND trkseg_csv_id = ? LIMIT 1';
        $this->trksegQuery = $this->connection->prepare($sql);

        $sql = 'INSERT IGNORE INTO gpsmap64.trkpt
          (trkseg_id, trkpt_csv_id, trkseg_csv_id, lat, lon, ele, time) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $this->trkptInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.trkpt WHERE time = ? LIMIT 1';
        $this->trkptQuery = $this->connection->prepare($sql);
    }

    private function importGarminExports() {
        $this->insertDirectory();
        $files = scandir(static::$importDir, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (preg_match('/^20[1-3]\d\-[-_\d]+\.csv$/', $file)) {
                $this->lookupFile($file);
                if (!$this->fileId) {
                    $this->insertFile($file);
                    $this->importFile($file);
                    $this->ingestGarminFile();
                }
            }
        }
    }

    private function ingestGarminFile() {
        $this->section = null;
        $this->header = [];
        foreach ($this->lines as $line) {
            if ($line === '') {
                $this->section = null;
            } elseif ($this->section === null) {
                $this->section = strtolower($line);
                $this->header = [];
            } else {
                $line = preg_replace('/,$/', '', $line);
                if (!count($this->header)) {
                    $this->header = str_getcsv($line);
                } else {
                    $fields = str_getcsv($line);
                    if (count($fields) !== count($this->header)) {
                        print_r(['Field count does not match header count',
                            'fields' => $fields, 'header' => $this->header]);
                        throw new StopException('Field count does not match header count');
                    }
                    $row = array_combine($this->header, $fields);
                    $this->ingestLine($row);
                }
            }
        }
    }

    private function ingestLine(array $row) {
        switch ($this->section) {
            case 'wpt':
                $this->insertWpt($row);
                break;
            case 'trk':
                $this->insertTrk($row);
                break;
            case 'trkseg':
                $this->insertTrkseg($row);
                break;
            case 'trkpt':
                $this->insertTrkpt($row);
                break;
            default:
                break;
        }
    }

    private function insertWpt(array $row) {
        $row = $this->normalizeRow($row);
        $id = $this->lookupWptUnique($row);
        if (!$id) {
            $time = date('Y-m-d H:i:s', strtotime($row['time']));
            $elevation = $this->toFeet($row['ele']);
            $parms = [
                $row['ID'], $row['lat'], $row['lon'], $elevation, $time,
                $row['name'], $row['cmt'], $row['desc'],
            ];
            $this->wptInsert->execute($parms);
        } else {
            $parms = [
                $row['ID'], $row['name'], $row['cmt'], $row['desc'], $id,
            ];
            $this->wptUpdate->execute($parms);
        }
    }

    private function lookupWptUnique(array $row) {
        $parms = [$row['lat'], $row['lon'], $row['ele']];
        $this->wptUnique->execute($parms);
        $result = $this->wptUnique->fetch('assoc');
        return (is_array($result) && array_key_exists('id', $result)) ? $result['id'] : 0;
    }

    private function insertTrk(array $row) {
        $id = $row['ID'];
        if (!$this->lookupTrack($id)) {
            $parms = [
                $this->fileId, $id, $row['name'], $row['cmt'], $row['desc'],
            ];
            $this->trkInsert->execute($parms);
        }
    }

    private function lookupTrack($id) {
        $this->trkQuery->execute([$this->fileId, $id]);
        $row = $this->trkQuery->fetch('assoc');
        return (is_array($row) && array_key_exists('id', $row)) ? $row['id'] : 0;
    }

    private function insertTrkseg(array $row) {
        $trkId = $this->lookupTrack($row['trkID']);
        $id = $row['ID'];
        $trksegId = $this->lookupTrkseg($trkId, $id);
        if (!$trksegId) {
            $parms = [
                $trkId, $id, $row['trkID'],
            ];
            $this->trksegInsert->execute($parms);
            $trksegId = $this->lookupTrkseg($trkId, $id);
        }
        $this->trksegMap[$this->fileId][$id] = $trksegId;
    }

    private function lookupTrkseg($trkId, $id) {
        $this->trksegQuery->execute([$trkId, $id]);
        $row = $this->trksegQuery->fetch('assoc');
        return (is_array($row) && array_key_exists('id', $row)) ? $row['id'] : 0;
    }

    private function insertTrkpt(array $row) {
        $row = $this->normalizeRow($row);
        $time = date('Y-m-d H:i:s', strtotime($row['time']));
        if (!$this->lookupTrkpt($time)) {
            $id = $row['ID'];
            $segCsv = $row['trksegID'];
            $trksegId = $this->trksegMap[$this->fileId][$segCsv];
            $elevation = $this->toFeet($row['ele']);
            $parms = [
                $trksegId, $id, $segCsv, $row['lat'], $row['lon'], $elevation, $time,
            ];
            $this->trkptInsert->execute($parms);
        }
    }

    private function lookupTrkpt($time) {
        $this->trkptQuery->execute([$time]);
        $row = $this->trkptQuery->fetch('assoc');
        return (is_array($row) && array_key_exists('id', $row)) ? $row['id'] : 0;
    }

}
