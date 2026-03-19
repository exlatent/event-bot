<?php

declare(strict_types=1);

namespace App\Model;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\Query;

abstract class AbstractRepository
{
    public function __construct(protected ConnectionInterface $connection) {}

    abstract protected function entityClass(): string;

    abstract protected function tableName(): string;

    protected function primaryKey(): string { return 'id';}

    public function findOne(array $params): ?AbstractEntity
    {
        $row = (new Query($this->connection))
            ->from($this->tableName())
            ->where($params)
            ->one();

        if (!$row) {
            return null;
        }

        return $this->fromRow($row);
    }

    public function findAll(): array
    {
        $rows = (new Query($this->connection))
            ->from($this->tableName())
            ->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

    public function findBy(array $params, int $limit = null): array
    {
        $query = (new Query($this->connection))
            ->from($this->tableName())
            ->where($params);

        if ($limit !== null) {
            $query->limit($limit);
        }

        $rows = $query->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

    public function save(AbstractEntity $entity): void
    {
        $data = get_object_vars($entity);
        $pk = $this->primaryKey();

        if (!empty($data[$pk])) {
            $this->connection->createCommand()
                ->update($this->tableName(), $data, [$pk => $data[$pk]])
                ->execute();
        } else {
            $this->connection->createCommand()
                ->insert($this->tableName(), $data)
                ->execute();
        }
    }

    public function delete(AbstractEntity $entity): void
    {
        $pk = $this->primaryKey();
        $data = get_object_vars($entity);

        $this->connection->createCommand()
            ->delete($this->tableName(), [$pk => $data[$pk]])
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
