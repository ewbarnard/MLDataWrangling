<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PowerSequence Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $time_begin
 * @property \Cake\I18n\FrozenTime $time_end
 *
 * @property \App\Model\Entity\Calculate[] $calculate
 */
class PowerSequence extends Entity
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
        'time_begin' => true,
        'time_end' => true,
        'calculate' => true
    ];
}
