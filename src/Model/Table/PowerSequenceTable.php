<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PowerSequence Model
 *
 * @property \App\Model\Table\CalculateTable|\Cake\ORM\Association\HasMany $Calculate
 *
 * @method \App\Model\Entity\PowerSequence get($primaryKey, $options = [])
 * @method \App\Model\Entity\PowerSequence newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PowerSequence[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PowerSequence|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PowerSequence patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PowerSequence[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PowerSequence findOrCreate($search, callable $callback = null, $options = [])
 */
class PowerSequenceTable extends Table
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

        $this->setTable('power_sequence');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Calculate', [
            'foreignKey' => 'power_sequence_id'
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
            ->dateTime('time_begin')
            ->requirePresence('time_begin', 'create')
            ->notEmpty('time_begin');

        $validator
            ->dateTime('time_end')
            ->requirePresence('time_end', 'create')
            ->notEmpty('time_end');

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
        $rules->add($rules->isUnique(['time_begin']));

        return $rules;
    }
}
