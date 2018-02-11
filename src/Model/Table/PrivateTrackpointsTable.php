<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PrivateTrackpoints Model
 *
 * @property \App\Model\Table\TrkptTable|\Cake\ORM\Association\BelongsTo $Trkpt
 *
 * @method \App\Model\Entity\PrivateTrackpoint get($primaryKey, $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PrivateTrackpoint findOrCreate($search, callable $callback = null, $options = [])
 */
class PrivateTrackpointsTable extends Table
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

        $this->setTable('private_trackpoints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trkpt', [
            'foreignKey' => 'trkpt_id',
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
        $rules->add($rules->existsIn(['trkpt_id'], 'Trkpt'));

        return $rules;
    }
}
