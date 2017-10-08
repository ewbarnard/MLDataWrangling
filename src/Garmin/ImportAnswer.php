<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 10:49 AM
 */

namespace App\Garmin;

use Cake\Console\Exception\StopException;
use Cake\Database\StatementInterface;

class ImportAnswer extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/ToClassify';

    /** @var StatementInterface */
    private $calculateQuery;

    /** @var StatementInterface */
    private $calculateUpdate;

    protected function main() {
        $this->prepareStatements();
        $this->importAnswer();
    }

    private function prepareStatements() {
        $sql = 'SELECT motion_id FROM gpsmap64.calculate WHERE id = ? LIMIT 1';
        $this->calculateQuery = $this->connection->prepare($sql);

        $sql = 'UPDATE gpsmap64.calculate SET motion_id = ? WHERE id = ? LIMIT 1';
        $this->calculateUpdate = $this->connection->prepare($sql);
    }

    private function importAnswer() {
        $this->insertDirectory();
        $files = scandir(static::$importDir, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (preg_match('/^answer_\w+\.csv/', $file)) {
                $this->lookupFile($file);
                if (!$this->fileId) {
                    $this->insertFile($file);
                    $this->importFile($file);
                    $this->ingestAnswerFile();
                }
            }
        }
    }

    private function ingestAnswerFile() {
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
        $id = (int)$row['id'];
        $motion = (string)$row['motion'];
        $motionId = (int)$this->lookupMotion($motion);
        $this->calculateQuery->execute([$id]);
        $result = $this->calculateQuery->fetch('assoc');
        if (is_array($result) && array_key_exists('motion_id', $result)) {
            $currentMotionId = (int)$result['motion_id'];
        } else {
            throw new StopException("No result for calculate.id = $id");
        }
        if ($motionId !== $currentMotionId) {
            $this->calculateUpdate->execute([$motionId, $id]);
        }
    }

}
