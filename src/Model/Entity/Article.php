<?php

namespace App\Model\Entity;

use Cake\Collection\Collection;
use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $tag_string
 * @property array $tags
 */
class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
        'tag_string' => true
    ];

    protected function _getTagString()
    {
        if(isset($this->_fields['tag_string'])){
            return $this->_fields['tag_string'];
        }
        if(empty($this->tags)){
            return '';
        }
        $tags = new Collection($this->tags);
        $str = $tags->reduce(function ($string, $tag) {
            return $string . $tag->title . ', ';
            }, '');

        return trim($str, ', ');
    }
}
