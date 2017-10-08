<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 10:54 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class ExportPart02Step01 extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/Part02/Step01';

    /** @var StatementInterface */
    private $splitQuery;

    /** @var StatementInterface */
    private $psQuery;

    /** @var StatementInterface */
    private $calculateQuery;

    private $slug = '';

    private $out;

    private $lineCount = 0;

    protected function main() {
        $this->prepareStatements();
        $this->exportStep();
    }

    private function prepareStatements() {
        $sql = 'SELECT slug, begin, end FROM gpsmap64.split ORDER BY begin LIMIT 50';
        $this->splitQuery = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.power_sequence WHERE time_begin >= ? AND time_end <= ? 
          ORDER BY time_begin LIMIT 10000';
        $this->psQuery = $this->connection->prepare($sql);

        $sql = 'SELECT
    c.id ,
	m. NAME motion ,
	c.power_sequence_id ,
	c.wpt_id ,
	d.feet distance ,
	c.feet ,
	c.seconds ,
	c.mph ,
	c.climb ,
	c.power_on_seconds ,
	c.is_sequence_start ,
	t.lat ,
	t.lon ,
	t.ele ,
	unix_timestamp(t.time) epoch
FROM
	gpsmap64.calculate c
INNER JOIN gpsmap64.trkpt t ON t.id = c.trkpt_id
INNER JOIN gpsmap64.motion m ON m.id = c.motion_id
INNER JOIN gpsmap64.distance d ON d.wpt_id = c.wpt_id
AND d.trkpt_id = c.trkpt_id
WHERE
	c.power_sequence_id = ?
ORDER BY
	t.time
LIMIT 20000';
        $this->calculateQuery = $this->connection->prepare($sql);
    }

    private function exportStep() {
        $this->insertDirectory();
        $this->loadSplits();
    }

    private function loadSplits() {
        $this->splitQuery->execute([]);
        $rows = $this->splitQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                $this->slug = $row['slug'];
                $this->loadSequences($row['begin'], $row['end']);
            }
        }
    }

    private function loadSequences($begin, $end) {
        $this->psQuery->execute([$begin, $end]);
        $rows = $this->psQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            $this->beginFile();
            $this->lineCount = 0;
            foreach ($rows as $row) {
                $this->loadTrackpoints($row['id']);
            }
        }
    }

    private function beginFile() {
        $path = static::$importDir . DIRECTORY_SEPARATOR . $this->slug . '.csv';
        $this->out = fopen($path, 'wb');
        $this->verbose("Creating $path");
    }

    private function loadTrackpoints($psId) {
        $this->calculateQuery->execute([$psId]);
        $rows = $this->calculateQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                if (!$this->lineCount) {
                    $this->writeHeader($row);
                }
                fputcsv($this->out, array_values($row));
                ++$this->lineCount;
            }
        }
    }

    /**
     * @param array $row
     */
    private function writeHeader(array $row) {
        fputcsv($this->out, array_keys($row));
        $list = [];
        foreach (array_keys($row) as $key) {
            $list[] = "'$key'";
        }
        $path = static::$importDir . DIRECTORY_SEPARATOR . 'features.txt';
        $line = 'features = data.drop([' . implode(', ', $list) . '], axis=1)' . PHP_EOL;
        file_put_contents($path, $line);
    }

}
