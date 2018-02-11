<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Calculate Entity
 *
 * @property int $id
 * @property int $trkpt_id
 * @property int $power_sequence_id
 * @property int $wpt_id
 * @property int $motion_id
 * @property int $feet
 * @property int $seconds
 * @property float $mph
 * @property float $climb
 * @property int $power_on_seconds
 * @property int $is_sequence_start
 * @property int $has_speed
 *
 * @property \App\Model\Entity\Trkpt $trkpt
 * @property \App\Model\Entity\PowerSequence $power_sequence
 * @property \App\Model\Entity\Wpt $wpt
 * @property \App\Model\Entity\Motion $motion
 */
class Calculate extends Entity
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
        'trkpt_id' => true,
        'power_sequence_id' => true,
        'wpt_id' => true,
        'motion_id' => true,
        'feet' => true,
        'seconds' => true,
        'mph' => true,
        'climb' => true,
        'power_on_seconds' => true,
        'is_sequence_start' => true,
        'has_speed' => true,
        'trkpt' => true,
        'power_sequence' => true,
        'wpt' => true,
        'motion' => true
    ];
}
