<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trk Model
 *
 * @property \App\Model\Table\FileTable|\Cake\ORM\Association\BelongsTo $Files
 * @property \App\Model\Table\TrksegTable|\Cake\ORM\Association\HasMany $Trkseg
 *
 * @method \App\Model\Entity\Trk get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trk newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Trk[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trk|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trk patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trk[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trk findOrCreate($search, callable $callback = null, $options = [])
 */
class TrkTable extends Table
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

        $this->setTable('trk');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('File', [
            'foreignKey' => 'file_id',
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
        $rules->add($rules->existsIn(['file_id'], 'File'));

        return $rules;
    }
}
