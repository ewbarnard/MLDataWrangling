<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 7:44 AM
 */

namespace App\Shell;

use App\Garmin\ImportGarmin;
use App\Garmin\NearestWaypoint;
use App\Garmin\SetSplit;
use App\Garmin\WaypointDistance;
use Cake\Console\Shell;

class GarminShell extends Shell {

    /** @noinspection PhpHierarchyChecksInspection */
    /**
     * @param array ...$args
     * @return void
     */
    public function main(...$args) {
        $this->verbose('Beginning import run', 2);

        SetSplit::run($this);
        ImportGarmin::run($this);
        WaypointDistance::run($this);
        NearestWaypoint::run($this);
    }
}
