<?php
class Home extends Controller
{
    public $home_model, $data = [];
    public function __construct()
    {
        $this->home_model = $this->model('HomeModel');
    }
    public function index()
    {
        $this->data['content'] = 'backend/dashboard/index';
        // $this->data['sub_content']['data'] = $this->model('HomeModel')->getList();
        $this->data['sub_content']['data'] = $this->home_model->getListProvince();
        $this->data['sub_content']['first'] = $this->home_model->getFirstProvince();
        $this->data['sub_content']['getFirst'] = $this->home_model->find(6);
        // $insertData = [
        //     'email' => '502@gmail.com',
        //     'password' => md5('123456789'),
        //     'phone' => '0902445843',
        //     'create_at' => date('y-m-d h:i:s'),
        // ];
        // $id = $this->home_model->lastInsertUsersId($insertData);
        // echo $id;
        // $this->home_model->deleteUsers(1);
        //var_dump($this->db);
        $this->data['sub_content']['tab'] = 'Dashboard';
        // $session = new Session();
        // $session->data('username');
        // Session::data('username', 'tuanle');
        // Session::data('info', ['user' => 'tuanle',
        //     'detail' => 'deail']);
        // Session::delete();
        // $sessionData = Session::data();
        // echo '<pre>';
        // print_r($sessionData);
        // echo '</pre>';
        // echo toSlug('Tuan le');

        $this->render('backend/layouts/admin_layout', $this->data);
    }
    public function detail($id = '', $slug = '')
    {
        echo 'id san pham: ' . $id . '<br/>';
        echo 'slug: ' . $slug;
    }
    public function search()
    {
        $keyword = $_GET['keyword'];
        echo 'tu khoa can tim: ' . $keyword;
    }
    public function getCategory()
    {
        $request = new Request();
        $this->renderView('backend/category/add');
    }
    public function postCategory()
    {
        $request = new Request();
        $data = $request->getField();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        $response = new Response();
        $response->redirect('home/getCategory');
    }
    public function getUser()
    {
        $this->data['msg'] = Session::flashData('msg');
        $this->render('backend/users/add', $this->data);
    }
    public function postUser()
    {
        $request = new Request();
        // set rules
        if ($request->isPost()) {
            $request->rules([
                'fullName' => 'required|min:5|max:30',
                'email' => 'required|email|min:6|unique:users:email',
                'age' => 'required|callback_check_age',
                'yeah' => 'required|callback_check_yeah',
                'password' => 'required|min:6',
                'confirm_password' => 'required|match:password',
            ]);
            // set message
            $request->message([
                'fullName.required' => 'Ho ten khong duoc de tronng',
                'fullName.min' => 'Ho ten phai lon hon 5 ky tu',
                'fullName.max' => 'ho ten phai nho hon 30 ky tu',
                'email.required' => 'email khong duoc de trong',
                'email.email' => 'dinh dang email khong hop le',
                'email.min' => 'email phai lon hon 6 ky tu',
                'email.unique' => 'email da ton tai trong he thong',
                'age.required' => 'tuoi khong duoc de trong',
                'age.callback_check_age' => 'tuoi khong duoc duoi 20',
                'yeah.required' => 'nam sinh khong duoc de trong',
                'yeah.callback_check_yeah' => 'nam sinh phai nho hon 1998',
                'password.required' => 'mat khau khong duoc de trong',
                'password.min' => 'mat khau phai lon hon 6 ky tu',
                'confirm_password.required' => 'nhap lai mat khau khong duoc de trong',
                'confirm_password.match' => 'mat khau chua khop',
            ]);
            $validate = $request->validate();
            if (!$validate) {
                Session::flashData('msg', 'da co loi xay ra vui long kiem tra lai');
            }
        }
        $response = new Response();
        $response->redirect('home/getUser');
    }
    public function check_age($age)
    {
        if ($age >= 20) {
            return true;
        }
        return false;
    }
    public function check_yeah($yeah)
    {
        if ($yeah <= 1998) {
            return true;
        }
        return false;
    }
}
