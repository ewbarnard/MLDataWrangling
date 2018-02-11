<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trkseg Entity
 *
 * @property int $id
 * @property int $trk_id
 * @property int $trkseg_csv_id
 * @property int $trk_csv_id
 *
 * @property \App\Model\Entity\Trk $trk
 * @property \App\Model\Entity\Trkpt[] $trkpt
 */
class Trkseg extends Entity
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
        'trk_id' => true,
        'trkseg_csv_id' => true,
        'trk_csv_id' => true,
        'trk' => true,
        'trkseg_csv' => true,
        'trk_csv' => true,
        'trkpt' => true
    ];
}
