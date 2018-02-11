<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trkpt Entity
 *
 * @property int $id
 * @property int $trkseg_id
 * @property int $trkpt_csv_id
 * @property int $trkseg_csv_id
 * @property float $lat
 * @property float $lon
 * @property float $ele
 * @property \Cake\I18n\FrozenTime $time
 *
 * @property \App\Model\Entity\Trkseg $trkseg
 * @property \App\Model\Entity\Calculate[] $calculate
 * @property \App\Model\Entity\Distance[] $distance
 * @property \App\Model\Entity\PrivateTrackpoint[] $private_trackpoints
 */
class Trkpt extends Entity
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
        'trkseg_id' => true,
        'trkpt_csv_id' => true,
        'trkseg_csv_id' => true,
        'lat' => true,
        'lon' => true,
        'ele' => true,
        'time' => true,
        'trkseg' => true,
        'trkpt_csv' => true,
        'trkseg_csv' => true,
        'calculate' => true,
        'distance' => true,
        'private_trackpoints' => true
    ];
}
