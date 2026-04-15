<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\Query;

abstract class AbstractRepository
{
    public function __construct(protected ConnectionInterface $connection) {}

    abstract protected function entityClass(): string;

    protected function primaryKey(): string { return 'id';}

    public static function tableName(): string {return '';}

    public function findOne(array $params): ?AbstractEntity
    {
        $row = (new Query($this->connection))
            ->from(static::tableName())
            ->where($params)
            ->one();

        if (!$row) {
            return null;
        }

        return $this->fromRow($row);
    }

    public function findAll($orderBy = null): array
    {
        $query = (new Query($this->connection))
            ->from(static::tableName());
        if($orderBy) {
            $query->orderBy($orderBy);
        }

        $rows = $query->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

    public function findBy(array $params, $limit = null): array
    {
        $query = (new Query($this->connection))
            ->from(static::tableName())
            ->where($params);

        if ($limit !== null) {
            $query->limit($limit);
        }

        $rows = $query->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

    public function findByPage(?array $params, $page = 1, $pageSize = 20)
    {
        $query = (new Query($this->connection))
            ->from(static::tableName());

        if($params) {
            $query->where($params);
        }

        $total = (clone $query)->count();

        $offset = ($page - 1) * $pageSize;

        $rows = $query
            ->offset($offset)
            ->limit($pageSize)
            ->all();


        return [
            'items' => array_map(fn($row) => $this->fromRow($row), $rows),
            'total' => $total,
        ];
    }

    public function save(AbstractEntity $entity): void
    {
        $data = get_object_vars($entity);
        $pk = $this->primaryKey();

        if (!empty($data[$pk])) {
            $this->connection->createCommand()
                ->update(static::tableName(), $data, [$pk => $data[$pk]])
                ->execute();
        } else {
            $this->connection->createCommand()
                ->insert(static::tableName(), $data)
                ->execute();
            $entity->{$pk} = (int)$this->connection->getLastInsertID();
        }
    }

    public function delete(AbstractEntity $entity): void
    {
        $pk = $this->primaryKey();
        $data = get_object_vars($entity);

        $this->connection->createCommand()
            ->delete(static::tableName(), [$pk => $data[$pk]])
            ->execute();
    }

    protected function fromRow(array $row): ?AbstractEntity
    {
        if (empty($row)) {return null;}

        $class = $this->entityClass();
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $args = [];

        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();
            $value = $row[$name] ?? null;

            $type = $param->getType()?->getName();

            if ($value !== null) {
                if ($type === 'int') {
                    $value = (int)$value;
                } elseif ($type === 'float') {
                    $value = (float)$value;
                } elseif ($type === 'bool') {
                    $value = (bool)$value;
                }
            }

            $args[] = $value;
        }

        return $reflection->newInstanceArgs($args);
    }
}
