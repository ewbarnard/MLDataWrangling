<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 10:03 AM
 */

namespace App\Garmin;

use Cake\Console\Exception\StopException;
use Cake\Database\StatementInterface;

class ImportPartOne extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/PartOne';

    /** @var StatementInterface */
    private $calculateQuery;

    /** @var StatementInterface */
    private $calculateUpdate;

    protected function main() {
        $this->prepareStatements();
        $this->importMotion();
    }

    private function prepareStatements() {
        $sql = 'SELECT
	gpsmap64.calculate.id ,
	gpsmap64.calculate.motion_id
FROM
	gpsmap64.trkpt
INNER JOIN gpsmap64.calculate ON gpsmap64.calculate.trkpt_id = gpsmap64.trkpt.id
WHERE
	time = ?
LIMIT 1';
        $this->calculateQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.calculate SET motion_id = ? WHERE id = ? LIMIT 1';
        $this->calculateUpdate = $this->connection->prepare($sql);
    }

    private function importMotion() {
        $this->insertDirectory();
        $files = scandir(static::$importDir, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (preg_match('/^category_\w+\.csv$/', $file)) {
                $this->lookupFile($file);
                if (!$this->fileId) {
                    $this->insertFile($file);
                    $this->importFile($file);
                    $this->ingestCategoryFile();
                }
            }
        }
    }

    private function ingestCategoryFile() {
        $this->header = [];
        foreach ($this->lines as $line) {
            if (!count($this->header)) {
                $this->header = str_getcsv($line);
            } else {
                $fields = str_getcsv($line);
                if (count($fields) !== count($this->header)) {
                    throw new StopException('Field count does not match header count: ' . $line);
                }
                $this->ingestLine(array_combine($this->header, $fields));
            }
        }
    }

    private function ingestLine(array $row) {
        $time = $row['time'];
        $this->calculateQuery->execute([$time]);
        $result = $this->calculateQuery->fetch('assoc');
        if (!(is_array($result) && array_key_exists('id', $result))) {
            throw new StopException("No trackpoint for $time");
        }
        $motionId = (int)$result['motion_id'];
        if ($motionId) {
            return;
        }
        $motionId = $this->lookupMotion($row['category']);
        $this->calculateUpdate->execute([$motionId, $result['id']]);
    }

}
