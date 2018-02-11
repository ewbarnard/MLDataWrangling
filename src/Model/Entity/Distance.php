<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Distance Entity
 *
 * @property int $id
 * @property int $wpt_id
 * @property int $trkpt_id
 * @property int $feet
 *
 * @property \App\Model\Entity\Wpt $wpt
 * @property \App\Model\Entity\Trkpt $trkpt
 */
class Distance extends Entity
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
        'wpt_id' => true,
        'trkpt_id' => true,
        'feet' => true,
        'wpt' => true,
        'trkpt' => true
    ];
}
