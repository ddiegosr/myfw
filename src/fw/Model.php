<?php

namespace MyFw;


abstract class Model
{
    private $attributes = [];
    protected static $table;
    protected static $fillable = [];

    /**
     * Intercepta a chamada $model->attribute = value e adiciona
     * no array de atributos
     *
     * @param $name
     * @param $value
     * @throws \ErrorException
     */
    public function __set($name, $value): void
    {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        } else {
            $trace = debug_backtrace();
            throw new \ErrorException("Undefined property: {$name}", 0, E_USER_ERROR, $trace[0]['file'],
                $trace[0]['line']);
        }

    }

    /**
     * Intercepta a chamada $model->attribute, verifica se o
     * atributo existe no array de atributos e retorna caso exista
     *
     * @param $attribute
     * @return mixed
     * @throws \ErrorException
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }

        $trace = debug_backtrace();
        throw new \ErrorException("Undefined property: {$attribute}", 0, E_USER_ERROR, $trace[0]['file'],
            $trace[0]['line']);
    }

    /**
     * Seta os $attributes ao array de atributos
     *
     * @param array $attributes
     */
    private function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Verifica se existe o atributo $fillable e caso exista
     * faz o filtro dos parametros da query exlcuindo
     * os que não estiverem no $fillable
     *
     * @param array $attributes
     * @return array
     */
    private static function filterFillable(array $attributes)
    {
        if (!empty(static::$fillable)){
            foreach ($attributes as $key => $attribute) {
                if (!in_array($key, static::$fillable)){
                    unset($attributes[$key]);
                }
            }
        }

        return $attributes;
    }

    /**
     * Inseri um registro na tabela em questão
     *
     * @param array $attributes
     * @return bool
     */
    public static function create(array $attributes): bool
    {
        $attributes = static::filterFillable($attributes);
        $pdo = Database::getConn();
        $sql = sprintf("INSERT INTO %s (", static::$table);
        $placeHolders = array_map(function ($name) {
            return ':' . $name;
        }, array_keys($attributes));

        for ($i = 0; $i < count($attributes); $i++) {
            $sql .= array_keys($attributes)[$i] . ', ';
        }

        $sql = substr($sql, 0, -2);
        $sql .= ") VALUES(";

        for ($i = 0; $i < count($placeHolders); $i++) {
            $sql .= array_values($placeHolders)[$i] . ', ';
        }

        $sql = substr($sql, 0, -2);
        $sql .= ");";

        $stmt = $pdo->prepare($sql);
        for ($i = 0; $i < count($placeHolders); $i++) {
            $stmt->bindValue($placeHolders[$i], array_values($attributes)[$i]);
        }

        return $stmt->execute();
    }

    /**
     * Executa a busca de um registro no banco de dados
     * baseado no parâmetro $id
     *
     * @param int $id
     * @return Model
     */
    public static function find(int $id): Model
    {
        $pdo = Database::getConn();
        $sql = sprintf("SELECT * FROM %s WHERE id=:id", static::$table);
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $attributes = (array)$stmt->fetch();

        $className = static::class;
        $novo = new $className;
        $novo->setAttributes($attributes);
        return $novo;
    }

    /**
     * Retorna todos os registros da tabela em questão
     *
     * @return array
     */
    public static function all(): array
    {
        $pdo = Database::getConn();
        $sql = sprintf("SELECT * FROM %s", static::$table);
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $className = static::class;
        for ($i = 0; $i < count($data); $i++) {
            $novo = new $className;
            $attributes = (array)$data[$i];
            $novo->setAttributes($attributes);
            $data[$i] = $novo;
        }

        return $data;
    }

    /**
     * Atualiza um registro na tabela em questão
     *
     * @param array $attributes
     * @return int
     */
    public function update(array $attributes): int
    {
        $attributes = static::filterFillable($attributes);
        $pdo = Database::getConn();
        $sql = sprintf("UPDATE %s SET ", static::$table);
        $placeHolders = array_map(function ($name) {
            return ':' . $name;
        }, array_keys($attributes));

        for ($i = 0; $i < count($attributes); $i++) {
            $sql .= sprintf("%s = :%s, ", array_keys($attributes)[$i], array_keys($attributes)[$i]);
        }

        $sql = substr($sql, 0, -2);
        $sql .= sprintf(' WHERE id=:id;');

        $stmt = $pdo->prepare($sql);
        for ($i = 0; $i < count($attributes); $i++) {
            $stmt->bindValue($placeHolders[$i], array_values($attributes)[$i]);
        }
        $stmt->bindValue(":id", $this->attributes['id']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Deleta um registro da tabela em questão
     *
     * @return int
     */
    public function delete(): int
    {
        $pdo = Database::getConn();
        $sql = sprintf("DELETE FROM %s WHERE id=:id", static::$table);
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $this->attributes['id']);
        $stmt->execute();

        return $stmt->rowCount();
    }
}