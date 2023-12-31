<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace illustrate\ORM\Test\Entities;

use illustrate\ORM\Core\DataMapper;
use illustrate\ORM\IDataMapper;
use illustrate\ORM\IEntityMapper;
use illustrate\ORM\Core\Query;
use illustrate\ORM\Entity;
use illustrate\ORM\IMappableEntity;

class User extends Entity implements IMappableEntity
{
    /** @var string */
    private $event = '';

    public function id(): int
    {
        return $this->orm()->getColumn('id');
    }

    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    public function age(): int
    {
        return $this->orm()->getColumn('age');
    }

    public function setId(int $id): self
    {
        $this->orm()->setColumn('id', $id);
        return $this;
    }

    public function setName(string $name): self
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    public function setAge(int $age): self
    {
        $this->orm()->setColumn('age', $age);
        return $this;
    }

    /**
     * @return Article[]
     */
    public function articles(): array
    {
        return $this->orm()->getRelated('articles');
    }

    public function publishedArticles(bool $prefix = false): array
    {
        return $this->orm()->getRelated($prefix ? 'pub:articles' : 'articles', function(Query $query){
            $query->where('published')->is(true);
        });
    }


    public function unpublishedArticles(bool $prefix = false): array
    {
        return $this->orm()->getRelated($prefix ? 'unpub:articles' : 'articles', function(Query $query){
            $query->where('published')->is(false);
        });
    }

    /**
     * @return Profile|null
     */
    public function profile()
    {
        return $this->orm()->getRelated('profile');
    }

    public function getEventName(): string
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->primaryKeyGenerator(function(DataMapper $data){
            return $data->getColumn('id');
        });

        $mapper->on('update', function(User $user, IDataMapper $data){
            $user->event = 'update';
        });

        $mapper->relation('articles')->hasMany(Article::class);
        $mapper->relation('profile')->hasOne(Profile::class);
    }

}