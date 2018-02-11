<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trkseg Model
 *
 * @property \App\Model\Table\TrkTable|\Cake\ORM\Association\BelongsTo $Trks
 * @property \App\Model\Table\TrkptTable|\Cake\ORM\Association\HasMany $Trkpt
 *
 * @method \App\Model\Entity\Trkseg get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trkseg newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Trkseg[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trkseg|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trkseg patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trkseg[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trkseg findOrCreate($search, callable $callback = null, $options = [])
 */
class TrksegTable extends Table
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

        $this->setTable('trkseg');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trk', [
            'foreignKey' => 'trk_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Trkpt', [
            'foreignKey' => 'trkseg_id'
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
        $rules->add($rules->existsIn(['trk_id'], 'Trks'));

        return $rules;
    }
}
