<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 3:19 PM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class ExportHike extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/Part02/Step01';

    private static $file = 'hike.csv';

    private $out;

    /** @var StatementInterface */
    private $query;

    public function main() {
        $this->prepareStatements();
        $this->exportPublic();
    }

    private function prepareStatements() {
        $sql = 'SELECT 
m.name motion,
t.lat, t.lon, t.ele, t.time ,
w.`name` nearest, d.feet distance,
c.feet, c.seconds, c.mph, c.climb
FROM gpsmap64.trkpt t 
INNER JOIN gpsmap64.calculate c ON c.trkpt_id = t.id 
INNER JOIN gpsmap64.wpt w ON w.id = c.wpt_id 
INNER JOIN gpsmap64.motion m ON m.id = c.motion_id 
INNER JOIN gpsmap64.distance d ON d.wpt_id = c.wpt_id AND d.trkpt_id = c.trkpt_id 
WHERE m.name = \'Hike\' 
ORDER BY t.time 
LIMIT 20000';
        $this->query = $this->connection->prepare($sql);
    }

    private function exportPublic() {
        $this->beginFile();
        $this->query->execute([]);
        $rows = $this->query->fetchAll('assoc');
        foreach ($rows as $key => $row) {
            if (!$key) {
                fputcsv($this->out, array_keys($row));
            }
            fputcsv($this->out, array_values($row));
        }
        $this->verbose(count($rows) . ' Hike rows');
    }

    private function beginFile() {
        $path = static::$importDir . DIRECTORY_SEPARATOR . static::$file;
        $this->out = fopen($path, 'wb');
        $this->verbose("Creating $path");
    }

}
