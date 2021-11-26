<?php
/*
extend class model
 */
class HomeModel extends Model
{
    private $_table = 'province';

    public function tableFill()
    {
        return 'province';
    }

    public function fieldFill()
    {
        return '_name, _code';
    }

    public function primaryKey()
    {
        return 'id';
    }

    public function getList()
    {
        $data = $this->db->query("SELECT * FROM mywebsite.$this->_table")->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public function getListProvince()
    {
        // $this->db->table('mywebsite', 'product')->innerJoin('category', 'product.category_id = category.id')->innerJoin('province', 'prodcut.province_id = province.id')->get();
        $data = $this->db->table('mywebsite', 'province')->where('id', '>', 3)->orderBy('id', 'ASC')->limit(5)->select('*')->get();
        return $data;
    }
    public function getFirstProvince()
    {
        $data = $this->db->table('mywebsite', 'province')->where('_name', '=', 'Hồ Chí Minh ')->select('')->first();
        return $data;
    }
    public function insertUsers($data)
    {
        $this->db->table('mywebsite', 'users')->update($data);
    }
    public function lastInsertUsersId($data)
    {
        $this->db->table('mywebsite', 'users')->insert($data);
        return $this->db->lastId();
    }
    public function updateUsers($data, $id)
    {
        $this->db->table('mywebsite', 'users')->where('id', '=', $id)->update($data);
    }
    public function deleteUsers($id)
    {
        $this->db->table('mywebsite', 'users')->where('id', '=', $id)->delete();
    }
}
