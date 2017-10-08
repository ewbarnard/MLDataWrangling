<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 8:02 AM
 */

namespace App\Garmin;

use Cake\Console\Shell;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Database\StatementInterface;

/** @noinspection LowerAccessLevelInspection */
abstract class AbstractBase {
    protected static $importDir = '/Users/ewb/Desktop/Garmin 64st/Exports';

    /** @var Connection */
    protected $connection;

    protected $fileId = 0;

    protected $lines = [];

    protected $miles = 0.0;

    protected $feet = 0;

    protected $seconds = 0;

    protected $mph = 0.0;

    protected $climb = 0.0;

    /** @var Shell */
    private $shell;

    /** @var StatementInterface */
    private $directoryInsert;

    /** @var StatementInterface */
    private $directoryQuery;

    private $directoryId = 0;

    /** @var StatementInterface */
    private $fileInsert;

    /** @var StatementInterface */
    private $fileQuery;

    /** @var StatementInterface */
    private $motionQuery;

    /** @var StatementInterface */
    private $motionInsert;

    protected function __construct(Shell $shell) {
        $this->shell = $shell;
        $this->basePrepareStatements();
    }

    private function basePrepareStatements() {
        $this->connection = ConnectionManager::get('default');
        $sql = 'INSERT INTO gpsmap64.directory
          (name) VALUES (?)';
        $this->directoryInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.directory WHERE name = ? LIMIT 1';
        $this->directoryQuery = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.file
          (directory_id, name) VALUES (?, ?)';
        $this->fileInsert = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.file WHERE directory_id = ? AND name = ? LIMIT 1';
        $this->fileQuery = $this->connection->prepare($sql);

        $sql = 'SELECT id FROM gpsmap64.motion WHERE name = ? LIMIT 1';
        $this->motionQuery = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.motion (name) VALUES (?)';
        $this->motionInsert = $this->connection->prepare($sql);
    }

    public static function run(Shell $shell) {
        $instance = new static($shell);
        $instance->main();
        $pattern = '/^.*' . preg_quote('\\', '/') . '/';
        $name = preg_replace($pattern, '', static::class);
        $shell->verbose($name . ': Done');
    }

    abstract protected function main();

    protected function insertDirectory() {
        $this->lookupDirectory();
        if (!$this->directoryId) {
            $this->directoryInsert->execute([static::$importDir]);
            $this->lookupDirectory();
        }
    }

    protected function lookupDirectory() {
        $this->directoryId = 0;
        $this->directoryQuery->execute([static::$importDir]);
        $row = $this->directoryQuery->fetch('assoc');
        if (is_array($row) && array_key_exists('id', $row)) {
            $this->directoryId = $row['id'];
        }
    }

    /**
     * @param string $file
     */
    protected function insertFile($file) {
        $this->fileInsert->execute([$this->directoryId, $file]);
        $this->lookupFile($file);
    }

    protected function lookupFile($file) {
        $this->fileId = 0;
        $this->fileQuery->execute([$this->directoryId, $file]);
        $row = $this->fileQuery->fetch('assoc');
        if (is_array($row) && array_key_exists('id', $row)) {
            $this->fileId = $row['id'];
        }
    }

    /**
     * @param string $file
     */
    protected function importFile($file) {
        $path = static::$importDir . DIRECTORY_SEPARATOR . $file;
        $this->verbose('Importing ' . $file);
        $page = file_get_contents($path);
        $this->lines = explode("\n", $page);
    }

    protected function verbose($message, $newlines = 1) {
        $this->shell->verbose($message, $newlines);
    }

    protected function normalizeRow(array $row) {
        if (array_key_exists('lat', $row)) {
            $row['lat'] = sprintf('%.6f', $row['lat']);
            $row['lon'] = sprintf('%.6f', $row['lon']);
        }
        if (array_key_exists('ele', $row)) {
            $row['ele'] = sprintf('%.2f', $row['ele']);
        }
        return $row;
    }

    protected function calculateSpeed(array $from, array $to) {
        $this->calculateDistance($from, $to);
        $epochFrom = strtotime($from['time']);
        $epochTo = strtotime($to['time']);
        $this->seconds = $epochTo - $epochFrom;
        $this->mph = $this->miles / $this->seconds * 3600;
        // Convert elevation change (meters) to feet
        $this->climb = ($to['ele'] - $from['ele']) * 3.28084;
    }

    /**
     * http://www.geodatasource.com/developers/php
     *
     * @param array $from
     * @param array $to
     * @return void
     */
    protected function calculateDistance(array $from, array $to) {
        $lat1 = $from['lat'];
        $lon1 = $from['lon'];
        $lat2 = $to['lat'];
        $lon2 = $to['lon'];
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $this->miles = abs($dist * 60 * 1.1515);
        $this->feet = $this->miles * 5280;
    }
}
