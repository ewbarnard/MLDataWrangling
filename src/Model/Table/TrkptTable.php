<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trkpt Model
 *
 * @property \App\Model\Table\TrksegTable|\Cake\ORM\Association\BelongsTo $Trksegs
 * @property \App\Model\Table\CalculateTable|\Cake\ORM\Association\HasMany $Calculate
 * @property \App\Model\Table\DistanceTable|\Cake\ORM\Association\HasMany $Distance
 * @property \App\Model\Table\PrivateTrackpointsTable|\Cake\ORM\Association\HasMany $PrivateTrackpoints
 *
 * @method \App\Model\Entity\Trkpt get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trkpt newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Trkpt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trkpt|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trkpt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trkpt[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trkpt findOrCreate($search, callable $callback = null, $options = [])
 */
class TrkptTable extends Table
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

        $this->setTable('trkpt');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trkseg', [
            'foreignKey' => 'trkseg_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Calculate', [
            'foreignKey' => 'trkpt_id'
        ]);
        $this->hasMany('Distance', [
            'foreignKey' => 'trkpt_id'
        ]);
        $this->hasMany('PrivateTrackpoints', [
            'foreignKey' => 'trkpt_id'
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
        $rules->add($rules->isUnique(['time']));
        $rules->add($rules->existsIn(['trkseg_id'], 'Trkseg'));

        return $rules;
    }
}
