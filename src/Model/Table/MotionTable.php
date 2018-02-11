<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Motion Model
 *
 * @property \App\Model\Table\CalculateTable|\Cake\ORM\Association\HasMany $Calculate
 *
 * @method \App\Model\Entity\Motion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Motion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Motion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Motion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Motion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Motion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Motion findOrCreate($search, callable $callback = null, $options = [])
 */
class MotionTable extends Table
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

        $this->setTable('motion');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Calculate', [
            'foreignKey' => 'motion_id'
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
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }
}
