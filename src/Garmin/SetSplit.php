<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 10/8/17
 * Time: 8:02 AM
 */

namespace App\Garmin;

use Cake\Database\StatementInterface;

class SetSplit extends AbstractBase {
    private static $split = [
        'mn_jul_train' => [
            'run_step' => 1,
            'type' => 'train',
            'begin' => '2017-01-01 00:00:00',
            'end' => '2017-07-31 23:59:59',
            'description' => 'Training data from Minnesota and North Dakota',
        ],
        'mn_aug_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-08-01 00:00:00',
            'end' => '2017-09-01 18:00:00',
            'description' => 'Test data from Minnesota and North Dakota',
        ],
        'wa_sep_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-09-01 18:00:01',
            'end' => '2017-09-10 20:00:00',
            'description' => 'Test data from Washington State',
        ],
        'mn_sep_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-09-10 20:00:01',
            'end' => '2017-09-21 16:00:00',
            'description' => 'Test data from Minnesota',
        ],
        'wi_sep_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-09-21 16:00:01',
            'end' => '2017-09-24 12:00:00',
            'description' => 'Test data from Wisconsin',
        ],
        'mn_oct_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-09-24 12:00:01',
            'end' => '2017-10-22 12:00:00',
            'description' => 'Test data from Minnesota',
        ],
        'nv_oct_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-10-22 12:00:01',
            'end' => '2017-10-26 17:00:00',
            'description' => 'Test data from Nevada',
        ],
        'mn_nov_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-10-26 17:00:01',
            'end' => '2017-11-12 08:00:00',
            'description' => 'Test data from Minnesota',
        ],
        'dc_nov_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-11-12 08:00:01',
            'end' => '2017-11-17 21:00:00',
            'description' => 'Test data from Washington, D.C.',
        ],
        'mn_dec_test' => [
            'run_step' => 1,
            'type' => 'test',
            'begin' => '2017-11-17 21:00:01',
            'end' => '2017-12-31 23:59:59',
            'description' => 'Test data from Minnesota',
        ],
        'wa_sep_public' => [
            'run_step' => 2,
            'type' => 'public',
            'begin' => '2017-09-04 12:37:00',
            'end' => '2017-09-09 21:00:00',
            'description' => 'Washington state public locations CDT',
        ],
        'mn_jul_train_knn' => [
            'run_step' => 3,
            'type' => 'train',
            'begin' => '2017-01-01 00:00:00',
            'end' => '2017-07-25 17:28:00',
            'description' => 'Training data from Minnesota and North Dakota',
        ],
        'mn_jul_test_knn' => [
            'run_step' => 3,
            'type' => 'test',
            'begin' => '2017-07-25 17:28:01',
            'end' => '2017-08-11 15:00:00',
            'description' => 'Test data from Minnesota and North Dakota',
        ],
        'mn_aug_test_knn' => [
            'run_step' => 3,
            'type' => 'test',
            'begin' => '2017-08-11 15:00:01',
            'end' => '2017-09-01 18:00:00',
            'description' => 'Test data from Minnesota and North Dakota',
        ],
        'wa_sep_test_knn' => [
            'run_step' => 3,
            'type' => 'test',
            'begin' => '2017-09-01 18:00:01',
            'end' => '2017-09-10 20:00:00',
            'description' => 'Test data from Washington State',
        ],
        'mn_sep_test_knn' => [
            'run_step' => 3,
            'type' => 'test',
            'begin' => '2017-09-10 20:00:01',
            'end' => '2017-09-21 16:00:00',
            'description' => 'Test data from Minnesota',
        ],
    ];

    /** @var StatementInterface */
    private $truncate;

    /** @var StatementInterface */
    private $insert;

    public function main() {
        $this->prepareStatements();
        $this->inserts();
    }

    private function inserts() {
        $this->truncate->execute([]);
        $this->verbose('Truncated table split');
        foreach (static::$split as $slug => $info) {
            $parms = [
                $info['run_step'],
                $info['type'], $slug, $info['begin'], $info['end'], $info['description'],
            ];
            $this->insert->execute($parms);
        }
    }

    private function prepareStatements() {
        $sql = 'truncate table gpsmap64.split';
        $this->truncate = $this->connection->prepare($sql);

        $sql = 'INSERT INTO gpsmap64.split (run_step, type, slug, begin, end, description) VALUES (?, ?, ?, ?, ?, ?)';
        $this->insert = $this->connection->prepare($sql);
    }

}
