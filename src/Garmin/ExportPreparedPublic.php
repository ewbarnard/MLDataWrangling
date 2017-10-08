<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 11:09 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class ExportPreparedPublic extends AbstractBase {
    protected static $importDir = '/Users/ewb/Dropbox/Talks/LearningML/MLDataWrangling/Data/Part02/Step01';

    private static $file = 'public.csv';

    private static $targetLineCount = 100000;

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
WHERE t.time >= \'2017-09-04 12:37:00\' 
AND t.time <= \'2017-09-09 21:00:00\' 
ORDER BY t.time 
LIMIT 10000';
        $this->query = $this->connection->prepare($sql);
    }

    private function exportPublic() {
        $lineCount = 0;
        $this->beginFile();
        $this->query->execute([]);
        $rows = $this->query->fetchAll('assoc');
        while ($lineCount < static::$targetLineCount) {
            foreach ($rows as $row) {
                if (!$lineCount) {
                    fputcsv($this->out, array_keys($row));
                }
                fputcsv($this->out, array_values($row));
                if (++$lineCount === static::$targetLineCount) {
                    return;
                }
            }
        }
    }

    private function beginFile() {
        $path = static::$importDir . DIRECTORY_SEPARATOR . static::$file;
        $this->out = fopen($path, 'wb');
        $this->verbose("Creating $path");
    }

}
