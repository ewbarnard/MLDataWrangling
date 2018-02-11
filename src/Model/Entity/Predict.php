<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Predict Entity
 *
 * @property int $id
 * @property string $file
 * @property float $all
 * @property float $drive
 * @property float $walk
 * @property float $hike
 * @property int $total_all
 * @property int $total_drive
 * @property int $total_walk
 * @property int $total_hike
 */
class Predict extends Entity
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
        'file' => true,
        'all' => true,
        'drive' => true,
        'walk' => true,
        'hike' => true,
        'total_all' => true,
        'total_drive' => true,
        'total_walk' => true,
        'total_hike' => true
    ];
}
