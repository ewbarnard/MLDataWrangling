<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * File Entity
 *
 * @property int $id
 * @property int $directory_id
 * @property string $name
 *
 * @property \App\Model\Entity\Directory $directory
 * @property \App\Model\Entity\Trk[] $trk
 */
class File extends Entity
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
        'directory_id' => true,
        'name' => true,
        'directory' => true,
        'trk' => true
    ];
}
