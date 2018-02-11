<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trk Entity
 *
 * @property int $id
 * @property int $file_id
 * @property int $trk_csv_id
 * @property string $name
 * @property string $cmt
 * @property string $desc
 *
 * @property \App\Model\Entity\File $file
 * @property \App\Model\Entity\Trkseg[] $trkseg
 */
class Trk extends Entity
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
        'file_id' => true,
        'trk_csv_id' => true,
        'name' => true,
        'cmt' => true,
        'desc' => true,
        'file' => true,
        'trk_csv' => true,
        'trkseg' => true
    ];
}
