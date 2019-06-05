<?php

namespace App\Core\Database;

use App\Core\App;
use App\Core\Request;

/**
 * Abstract sql queries from the application
 */
class QueryBuilder
{
    protected $pdo;

    protected $resources;

    protected $put;

    protected $query;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Prepare QueryBuilder to get data from table
     *
     * @param  string $table
     * @return QueryBuilder
     */
    public function all($table)
    {
        $this->query = "select * from {$table}";

        return $this;
    }

    /**
     * Apply order to the query
     *
     * @param  string $order
     * @return QueryBuilder
     */
    public function order($order)
    {
        $this->query .= " ORDER BY {$order}";

        return $this;
    }

    /**
     * Execute the actual query and return the results
     *
     * @return array
     */
    public function get()
    {
        $this->statement = $this->pdo->prepare($this->query);

        $this->statement->execute();

        $data = $this->statement->fetchAll(\PDO::FETCH_CLASS);

        return $data;
    }

    /**
     * Get table element by id
     *
     * @param  string $table
     * @param  int $id
     * @return object
     */
    public function getById($table, $id)
    {
        $statement = $this->pdo->prepare("select * from {$table} where id={$id}");

        $statement->execute();

        $mainData = $statement->fetchAll(\PDO::FETCH_CLASS);

        return $mainData[0];
    }

    /**
     * Insert an element in the database
     *
     * @param  string $table
     * @param  array $data
     * @return object
     */
    public function save($table, $data)
    {
        $attributes = array_keys($data);
        $columns = implode(",", $attributes);
        $binds = ":" . implode(", :", $attributes);

        $statement = $this->pdo->prepare("INSERT INTO {$table} ({$columns})
            VALUES ({$binds})");

        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();

        $addedModel = $this->getById($table, $this->pdo->lastInsertId());

        return $addedModel;
    }

    /**
     * Update an element on the database
     *
     * @param  string $table
     * @param  array $data
     * @return object
     */
    public function update($table, $data)
    {
        $attributes = array_keys($data);
        $columns = implode(",", $attributes);
        $binds = ":" . implode(", :", $attributes);

        foreach ($attributes as $attribute) {
            $setAttributes .= "{$attribute}=:{$attribute},";
        }

        $setAttributes = rtrim($setAttributes, ',');

        $statement = $this->pdo->prepare("UPDATE {$table} SET {$setAttributes} WHERE id=:id");

        foreach ($attributes as $attribute) {
            $statement->bindParam(":{$attribute}", $data[$attribute]);
        }

        $statement->bindParam(":id", $data['id']);

        $statement->execute();

        return $this->getById($table, $data['id']);
    }

    /**
     * Delete element from database
     *
     * @param  string $table
     * @param  int $id
     * @return boolean
     */
    public function delete($table, $id)
    {
        try {
            $statement = $this->pdo->prepare("DELETE FROM {$table} WHERE id=:id");

            $statement->bindParam(":id", $id);

            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Delete all elements from table
     *
     * @param  string $table
     * @return boolean
     */
    public function deleteAll($table)
    {
        try {
            $statement = $this->pdo->prepare("DELETE FROM {$table}");

            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (Exception $exception) {
            return false;
        }
    }
}
