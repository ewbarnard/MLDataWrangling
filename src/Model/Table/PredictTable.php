<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Predict Model
 *
 * @method \App\Model\Entity\Predict get($primaryKey, $options = [])
 * @method \App\Model\Entity\Predict newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Predict[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Predict|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Predict patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Predict[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Predict findOrCreate($search, callable $callback = null, $options = [])
 */
class PredictTable extends Table
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

        $this->setTable('predict');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('file')
            ->maxLength('file', 255)
            ->requirePresence('file', 'create')
            ->notEmpty('file')
            ->add('file', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->decimal('all')
            ->requirePresence('all', 'create')
            ->notEmpty('all');

        $validator
            ->decimal('drive')
            ->requirePresence('drive', 'create')
            ->notEmpty('drive');

        $validator
            ->decimal('walk')
            ->requirePresence('walk', 'create')
            ->notEmpty('walk');

        $validator
            ->decimal('hike')
            ->requirePresence('hike', 'create')
            ->notEmpty('hike');

        $validator
            ->integer('total_all')
            ->requirePresence('total_all', 'create')
            ->notEmpty('total_all');

        $validator
            ->integer('total_drive')
            ->requirePresence('total_drive', 'create')
            ->notEmpty('total_drive');

        $validator
            ->integer('total_walk')
            ->requirePresence('total_walk', 'create')
            ->notEmpty('total_walk');

        $validator
            ->integer('total_hike')
            ->requirePresence('total_hike', 'create')
            ->notEmpty('total_hike');

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
        $rules->add($rules->isUnique(['file']));

        return $rules;
    }
}
