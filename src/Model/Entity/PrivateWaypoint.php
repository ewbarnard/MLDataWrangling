<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PrivateWaypoint Entity
 *
 * @property int $id
 * @property int $wpt_id
 * @property int $is_private
 * @property string $wpt_name
 *
 * @property \App\Model\Entity\Wpt $wpt
 */
class PrivateWaypoint extends Entity
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
        'is_private' => true,
        'wpt_name' => true,
        'wpt' => true
    ];
}
