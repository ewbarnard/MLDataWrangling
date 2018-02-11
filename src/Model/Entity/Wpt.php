<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Wpt Entity
 *
 * @property int $id
 * @property int $wpt_csv_id
 * @property float $lat
 * @property float $lon
 * @property float $ele
 * @property \Cake\I18n\FrozenTime $time
 * @property string $name
 * @property string $cmt
 * @property string $desc
 *
 * @property \App\Model\Entity\Calculate[] $calculate
 * @property \App\Model\Entity\Distance[] $distance
 * @property \App\Model\Entity\PrivateWaypoint[] $private_waypoints
 */
class Wpt extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'wpt_csv_id' => true,
        'lat' => true,
        'lon' => true,
        'ele' => true,
        'time' => true,
        'name' => true,
        'cmt' => true,
        'desc' => true,
        'wpt_csv' => true,
        'calculate' => true,
        'distance' => true,
        'private_waypoints' => true
    ];
}
