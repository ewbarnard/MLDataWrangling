<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PrivateWaypoints Model
 *
 * @property \App\Model\Table\WptTable|\Cake\ORM\Association\BelongsTo $Wpt
 *
 * @method \App\Model\Entity\PrivateWaypoint get($primaryKey, $options = [])
 * @method \App\Model\Entity\PrivateWaypoint newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PrivateWaypoint[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PrivateWaypoint|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrivateWaypoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PrivateWaypoint[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PrivateWaypoint findOrCreate($search, callable $callback = null, $options = [])
 */
class PrivateWaypointsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('private_waypoints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Wpt', [
            'foreignKey' => 'wpt_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('is_private', 'create')
            ->notEmpty('is_private');

        $validator
            ->scalar('wpt_name')
            ->maxLength('wpt_name', 255)
            ->requirePresence('wpt_name', 'create')
            ->notEmpty('wpt_name');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['wpt_id'], 'Wpt'));

        return $rules;
    }
}
