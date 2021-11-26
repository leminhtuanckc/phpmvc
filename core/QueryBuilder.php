<?php
/**
 *
 */
trait QueryBuilder
{
    public $tableName = '';
    public $where = '';
    public $operator = '';
    public $selectField = '';
    public $limit = '';
    public $orderBy = '';
    public $innerJoin = '';

    public function table($db, $tableName)
    {
        $this->tableName = ' ' . $db . '.' . $tableName . ' ';
        return $this;
    }

    public function where($field, $compare, $value)
    {
        if (empty($this->where)) {
            $this->operator = ' WHERE ';
        } else {
            $this->operator = ' AND ';
        }
        $this->where .= "$this->operator $field $compare '$value'";
        return $this;
    }

    public function orWhere($field, $compare, $value)
    {
        if (empty($this->where)) {
            $this->operator = ' WHERE ';
        } else {
            $this->operator = ' OR ';
        }
        $this->where .= "$this->operator $field $compare '$value'";
        return $this;
    }

    public function innerJoin($tableName, $relationship)
    {
        $this->innerJoin .= " INNER JOIN " . $tableName . " ON " . $relationship . " ";
        return $this;
    }

    public function insert($data)
    {
        $tableName = $this->tableName;
        $insertStatus = $this->insertData($tableName, $data);
        return $insertStatus;
    }

    public function lastId()
    {
        return $this->lastInsertId();
    }

    public function update($data)
    {
        $whereUpdate = str_replace('WHERE', '', $this->where);
        $whereUpdate = trim($whereUpdate);
        $tableName = $this->tableName;
        $statusUpdate = $this->updateData($tableName, $data, $whereUpdate);
        return $statusUpdate;
    }

    public function delete()
    {
        $whereDelete = str_replace('WHERE', '', $this->where);
        $whereDelete = trim($whereDelete);
        $tableName = $this->tableName;
        $statusDelete = $this->deleteData($tableName, $whereDelete);
        return $statusDelete;
    }

    public function select($field)
    {
        if (empty($field)) {
            $this->selectField = '*';
        } else {
            $this->selectField = $field;
        }
        return $this;
    }

    public function limit($number, $offset = 0)
    {
        $this->limit = "LIMIT $offset, $number";
        return $this;
    }

    public function orderBy($field, $type = 'ASC')
    {
        $fieldArr = array_filter(explode(',', $field));
        if (!empty($fieldArr) && count($fieldArr) >= 2) {
            # sql order by multi
            $this->orderBy = " ORDER BY " . implode(',', $fieldArr);
        } else {
            $this->orderBy = " ORDER BY " . $field . " " . $type;
        }
        return $this;
    }

    public function get()
    {
        $sqlQuery = "SELECT $this->selectField FROM $this->tableName $this->innerJoin $this->where $this->orderBy $this->limit";
        $sqlQuery = trim($sqlQuery);
        $query = $this->query($sqlQuery);
        // reset field
        $this->resetQuery();
        if (!empty($query)) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function first()
    {
        $sqlQuery = "SELECT $this->selectField FROM $this->tableName $this->where";
        $query = $this->query($sqlQuery);
        // reset field
        $this->resetQuery();
        if (!empty($query)) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function resetQuery()
    {
        $this->tableName = '';
        $this->where = '';
        $this->selectField = '';
        $this->operator = '';
        $this->limit = '';
        $this->innerJoin = '';
    }
}
