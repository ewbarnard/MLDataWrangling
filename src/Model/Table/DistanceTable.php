<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Distance Model
 *
 * @property \App\Model\Table\WptTable|\Cake\ORM\Association\BelongsTo $Wpts
 * @property \App\Model\Table\TrkptTable|\Cake\ORM\Association\BelongsTo $Trkpts
 *
 * @method \App\Model\Entity\Distance get($primaryKey, $options = [])
 * @method \App\Model\Entity\Distance newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Distance[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Distance|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Distance patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Distance[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Distance findOrCreate($search, callable $callback = null, $options = [])
 */
class DistanceTable extends Table
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

        $this->setTable('distance');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Wpt', [
            'foreignKey' => 'wpt_id',
            'joinType' => 'INNER'
        ]);
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
            ->integer('feet')
            ->requirePresence('feet', 'create')
            ->notEmpty('feet');

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
        $rules->add($rules->existsIn(['trkpt_id'], 'Trkpt'));

        return $rules;
    }
}
