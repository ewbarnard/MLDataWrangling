<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Wpt Model
 *
 * @property \App\Model\Table\CalculateTable|\Cake\ORM\Association\HasMany $Calculate
 * @property \App\Model\Table\DistanceTable|\Cake\ORM\Association\HasMany $Distance
 * @property \App\Model\Table\PrivateWaypointsTable|\Cake\ORM\Association\HasMany $PrivateWaypoints
 *
 * @method \App\Model\Entity\Wpt get($primaryKey, $options = [])
 * @method \App\Model\Entity\Wpt newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Wpt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Wpt|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Wpt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Wpt[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Wpt findOrCreate($search, callable $callback = null, $options = [])
 */
class WptTable extends Table
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

        $this->setTable('wpt');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Calculate', [
            'foreignKey' => 'wpt_id'
        ]);
        $this->hasMany('Distance', [
            'foreignKey' => 'wpt_id'
        ]);
        $this->hasMany('PrivateWaypoints', [
            'foreignKey' => 'wpt_id'
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
            ->decimal('lat')
            ->requirePresence('lat', 'create')
            ->notEmpty('lat');

        $validator
            ->decimal('lon')
            ->requirePresence('lon', 'create')
            ->notEmpty('lon');

        $validator
            ->decimal('ele')
            ->requirePresence('ele', 'create')
            ->notEmpty('ele');

        $validator
            ->dateTime('time')
            ->requirePresence('time', 'create')
            ->notEmpty('time');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('cmt')
            ->maxLength('cmt', 255)
            ->requirePresence('cmt', 'create')
            ->notEmpty('cmt');

        $validator
            ->scalar('desc')
            ->maxLength('desc', 255)
            ->requirePresence('desc', 'create')
            ->notEmpty('desc');

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

        return $rules;
    }
}
