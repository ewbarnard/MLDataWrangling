<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PrivateTrackpoint Entity
 *
 * @property int $id
 * @property int $trkpt_id
 * @property int $is_private
 *
 * @property \App\Model\Entity\Trkpt $trkpt
 */
class PrivateTrackpoint extends Entity
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
        'is_private' => true,
        'trkpt' => true
    ];
}
