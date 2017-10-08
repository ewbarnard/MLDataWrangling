<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 11:06 AM
 */

namespace App\Garmin;

use Cake\Console\Exception\StopException;
use Cake\Database\StatementInterface;

class ImportPredict extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/Part02/Step02';

    private static $reset = [
        'all' => ['total' => 0, 'correct' => 0],
        'drive' => ['total' => 0, 'correct' => 0],
        'walk' => ['total' => 0, 'correct' => 0],
        'hike' => ['total' => 0, 'correct' => 0],
    ];

    /** @var StatementInterface */
    private $insert;

    /** @var StatementInterface */
    private $truncate;

    private $counts = [];

    protected function main() {
        $this->prepareStatements();
        $this->importPredict();
    }

    private function prepareStatements() {
        $this->truncate = $this->connection->prepare('truncate table gpsmap64.predict');

        $sql = 'INSERT INTO gpsmap64.predict 
          (file, `all`, drive, walk, hike, total_all, total_drive, total_walk, total_hike) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->insert = $this->connection->prepare($sql);
    }

    private function importPredict() {
        $this->truncate->execute([]);
        $this->insertDirectory();
        $files = scandir(static::$importDir, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (preg_match('/^predict_.*\.csv/', $file)) {
                $this->importFile($file);
                $this->ingestPredictFile($file);
            }
        }
    }

    private function ingestPredictFile($file) {
        $this->counts = static::$reset;
        $this->header = [];
        foreach ($this->lines as $line) {
            if (!count($this->header)) {
                $this->header = str_getcsv($line);
            } else {
                if ($line !== '') {
                    $fields = str_getcsv($line);
                    if (count($fields) !== count($this->header)) {
                        throw new StopException('Field count does not match header count: ' . $line);
                    }
                    $this->ingestLine(array_combine($this->header, $fields));
                }
            }
        }
        $this->summarize($file);
    }

    private function ingestLine(array $row) {
        $motion = strtolower($row['motion']);
        $predict = strtolower($row['predict']);
        ++$this->counts['all']['total'];
        ++$this->counts[$motion]['total'];
        if ($motion === $predict) {
            ++$this->counts['all']['correct'];
            ++$this->counts[$motion]['correct'];
        }
    }

    private function summarize($file) {
        $parms = [
            $file,
            $this->percent('all'),
            $this->percent('drive'),
            $this->percent('walk'),
            $this->percent('hike'),
            $this->counts['all']['total'],
            $this->counts['drive']['total'],
            $this->counts['walk']['total'],
            $this->counts['hike']['total'],
        ];
        $this->insert->execute($parms);
    }

    private function percent($key) {
        if (!$this->counts[$key]['total']) {
            return '0.00';
        }
        return sprintf('%.2f', $this->counts[$key]['correct'] / $this->counts[$key]['total'] * 100.0);
    }

}
