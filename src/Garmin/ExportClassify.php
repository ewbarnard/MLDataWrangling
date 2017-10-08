<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 10:44 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class ExportClassify extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/ToClassify';

    /** @var StatementInterface */
    private $psQuery;

    /** @var StatementInterface */
    private $calculateQuery;

    private $psIds = [];

    private $lineCount = 0;

    private $out;

    protected function main() {
        $this->prepareStatements();
        $this->exportToClassify();
    }

    private function prepareStatements() {
        $sql = 'SELECT
	id
FROM
	gpsmap64.power_sequence
WHERE
	power_sequence.id IN(
		SELECT DISTINCT
			power_sequence_id
		FROM
			gpsmap64.calculate
		WHERE
			motion_id = 0
	)
ORDER BY
	time_begin';
        $this->psQuery = $this->connection->prepare($sql);

        $sql = 'SELECT
	calculate.id ,
	power_sequence_id ,
	calculate.feet ,
	seconds ,
	climb ,
	trkpt.time ,
	distance.feet feet_from ,
	wpt. NAME nearest ,
	COALESCE(motion. NAME , \'\') current ,
	COALESCE(motion. NAME , \'Drive\') motion ,
	mph ,
	is_sequence_start ,
	power_on_seconds
FROM
	gpsmap64.calculate
INNER JOIN gpsmap64.wpt ON gpsmap64.calculate.wpt_id = gpsmap64.wpt.id
LEFT JOIN gpsmap64.motion ON gpsmap64.calculate.motion_id = gpsmap64.motion.id
INNER JOIN gpsmap64.trkpt ON gpsmap64.calculate.trkpt_id = gpsmap64.trkpt.id
INNER JOIN gpsmap64.distance ON gpsmap64.calculate.wpt_id = gpsmap64.distance.wpt_id
AND gpsmap64.calculate.trkpt_id = gpsmap64.distance.trkpt_id
WHERE
	power_sequence_id = ?
ORDER BY
	trkpt.time
LIMIT 20000;';
        $this->calculateQuery = $this->connection->prepare($sql);
    }

    private function exportToClassify() {
        $this->insertDirectory();
        $this->loadPowerSequences();
        if (count($this->psIds)) {
            foreach ($this->psIds as $psId) {
                $this->exportPowerSequence($psId);
            }
        }
    }

    private function loadPowerSequences() {
        $this->psQuery->execute([]);
        $rows = $this->psQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                $this->psIds[] = (int)$row['id'];
            }
        }
    }

    private function exportPowerSequence($psId) {
        if (!$this->lineCount) {
            $this->beginFile($psId);
        }
        $this->calculateQuery->execute([$psId]);
        $rows = $this->calculateQuery->fetchAll('assoc');
        if (is_array($rows) && count($rows)) {
            foreach ($rows as $row) {
                if (!$this->lineCount) {
                    fputcsv($this->out, array_keys($row));
                }
                fputcsv($this->out, array_values($row));
                ++$this->lineCount;
            }
        }
        if ($this->lineCount > 10000) {
            $this->lineCount = 0; // Will start new export file
        }
    }

    /**
     * @param $psId
     */
    private function beginFile($psId) {
        $path = static::$importDir . DIRECTORY_SEPARATOR . 'classify_' .
            sprintf('%04d', $psId) . '.csv';
        $this->out = fopen($path, 'wb');
        $this->verbose("Creating $path");
    }

}
