<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Calculate Model
 *
 * @property \App\Model\Table\TrkptTable|\Cake\ORM\Association\BelongsTo $Trkpts
 * @property \App\Model\Table\PowerSequenceTable|\Cake\ORM\Association\BelongsTo $PowerSequences
 * @property \App\Model\Table\WptTable|\Cake\ORM\Association\BelongsTo $Wpts
 * @property \App\Model\Table\MotionTable|\Cake\ORM\Association\BelongsTo $Motions
 *
 * @method \App\Model\Entity\Calculate get($primaryKey, $options = [])
 * @method \App\Model\Entity\Calculate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Calculate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Calculate|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Calculate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Calculate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Calculate findOrCreate($search, callable $callback = null, $options = [])
 */
class CalculateTable extends Table
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

        $this->setTable('calculate');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trkpt', [
            'foreignKey' => 'trkpt_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('PowerSequence', [
            'foreignKey' => 'power_sequence_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Wpt', [
            'foreignKey' => 'wpt_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Motion', [
            'foreignKey' => 'motion_id',
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

        $validator
            ->requirePresence('seconds', 'create')
            ->notEmpty('seconds');

        $validator
            ->decimal('mph')
            ->requirePresence('mph', 'create')
            ->notEmpty('mph');

        $validator
            ->decimal('climb')
            ->requirePresence('climb', 'create')
            ->notEmpty('climb');

        $validator
            ->integer('power_on_seconds')
            ->requirePresence('power_on_seconds', 'create')
            ->notEmpty('power_on_seconds');

        $validator
            ->requirePresence('is_sequence_start', 'create')
            ->notEmpty('is_sequence_start');

        $validator
            ->requirePresence('has_speed', 'create')
            ->notEmpty('has_speed');

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
        $rules->add($rules->existsIn(['power_sequence_id'], 'PowerSequence'));
        $rules->add($rules->existsIn(['wpt_id'], 'Wpt'));
        $rules->add($rules->existsIn(['motion_id'], 'Motion'));

        return $rules;
    }
}
