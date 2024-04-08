<?php

namespace App\Model\Table;

use App\Model\Entity\Article;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;


class ArticlesTable extends Table {
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags');

    }

    public function beforeSave(EventInterface $event, Article $entity, $options): void
    {
        if($entity->isNew() && !$entity->slug){
            $sluggedTitle = Text::slug($entity->title);
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->notEmptyString('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)
            ->notEmptyString('body')
            ->minLength('body', 10);

        return $validator;
    }

    public function findTagged(Query $query, array $options): Query
    {
        $columns = [
            'Articles.id',
            'Articles.user_id',
            'Articles.title',
            'Articles.body',
            'Articles.published',
            'Articles.created',
            'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if(empty($options['tags'])){
            // If there are no tags provided, find articles that have no tags.
            $query
                ->leftJoinWith('Tags')->where(['Tags.title IS' => null]);
        }else{
            $query->innerJoinWith('Tags')->where(['Tags.title IN' => $options['tags']]);
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }
}
