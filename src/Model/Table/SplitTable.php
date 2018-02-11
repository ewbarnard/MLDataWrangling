<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Split Model
 *
 * @method \App\Model\Entity\Split get($primaryKey, $options = [])
 * @method \App\Model\Entity\Split newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Split[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Split|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Split patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Split[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Split findOrCreate($search, callable $callback = null, $options = [])
 */
class SplitTable extends Table
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

        $this->setTable('split');
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('run_step', 'create')
            ->notEmpty('run_step');

        $validator
            ->scalar('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 32)
            ->requirePresence('slug', 'create')
            ->notEmpty('slug')
            ->add('slug', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->dateTime('begin')
            ->requirePresence('begin', 'create')
            ->notEmpty('begin');

        $validator
            ->dateTime('end')
            ->requirePresence('end', 'create')
            ->notEmpty('end');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->requirePresence('description', 'create')
            ->notEmpty('description');

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
        $rules->add($rules->isUnique(['slug']));

        return $rules;
    }
}
