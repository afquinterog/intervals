<?php

namespace App\Core\Database;

use App\Core\App;
use App\Core\Validator;
use App\Core\Database\Inflect;

/**
 * Model base class that include operations to store information on the persistence layer.
 * All the entities that require store information on the database should extend this class
 */
class Model
{
    /**
     * Create a model using the entity attributes.
     *
     * @param array $attributes
     */
    public function __construct($attributes=[])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get all the models from the database
     *
     * @return array
     */
    public function all()
    {
        $tableName = $this->getTableName();

        $data = App::get("database")->all($tableName);

        return $data;
    }

    /**
     * Find the model by id
     *
     * @param  int $id
     * @return Model
     */
    public static function find($id)
    {
        $table = strtolower(Inflect::pluralize(end(explode("\\", get_called_class()))));

        $data = App::get("database")->getById($table, $id);

        return $data[0];
    }

    /**
    * Delete a model by id
    *
    * @param int $id
    */
    public static function delete($id)
    {
        $table = strtolower(Inflect::pluralize(end(explode("\\", get_called_class()))));

        $result = false;
        if ($id > 0) {
            $result = App::get("database")->delete($table, $id);
        }

        return $result;
    }

    /**
    * Delete all the models on the database
    */
    public static function deleteAll()
    {
        $table = strtolower(Inflect::pluralize(end(explode("\\", get_called_class()))));

        $result = App::get("database")->deleteAll($table);

        return $result;
    }

    /**
     * Save/update a model on the persistence layer
     *
     * @return Model
     */
    public function save()
    {
        $tableName = $this->getTableName();

        if ($this->id) {
            $result = App::get("database")->update($tableName, (array) $this);
        } else {
            $result = App::get("database")->save($tableName, $this->getAttributes());

            $model = (array) $result;

            $this->fireEvents($model, "saved");
        }

        return (array) $result;
    }

    /**
     * Validate the model attributes
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->validations as $attribute => $validation) {
            $result = Validator::$validation($this->$attribute);
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Guess the table name based on the class name.
     *
     * @return string
     */
    protected function getTableName()
    {
        return strtolower(Inflect::pluralize(end(explode("\\", get_class($this)))));
    }

    /**
     * Return the model attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $getFields = function ($obj) {
            return get_object_vars($obj);
        };

        $attributes = $getFields($this);

        //Remove events and validations
        unset($attributes["dispatchesEvents"]);
        unset($attributes["validations"]);

        return $attributes;
    }

    /**
     * @param  class $model
     * @param  string $action
     * @return void
     */
    public function fireEvents($model, $action)
    {
        $class = get_class($this);

        $model = new $class($model);

        foreach ($this->dispatchesEvents as $event => $value) {
            if ($event == $action) {
                $listener = new $value;
                $listener->handle($model);
            }
        }
    }
}
