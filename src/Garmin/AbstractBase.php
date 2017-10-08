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

abstract class AbstractBase {
    protected static $importDir = '/Users/ewb/Desktop/Garmin 64st/Exports';

    /** @var Connection */
    protected $connection;
    /** @var Shell */
    private $shell;

    protected function __construct(Shell $shell) {
        $this->shell = $shell;
        $this->connection = ConnectionManager::get('default');
    }
    abstract protected function main();

    public static function run(Shell $shell) {
        $instance = new static($shell);
        $instance->main();
        $pattern = '/^.*' . preg_quote('\\', '/') . '/';
        $name = preg_replace($pattern, '', static::class);
        $shell->verbose($name . ': Done');
    }

    protected function verbose($message, $newlines = 1) {
        $this->shell->verbose($message, $newlines);
    }
}
