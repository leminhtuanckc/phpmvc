<?php
class Request
{
    private $__rules = [], $__message = [], $__errors = [];
    public $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    public function isPost()
    {
        if ($this->getMethod() == 'post') {
            return true;
        }
        return false;
    }
    public function isGet()
    {
        if ($this->getMethod() == 'get') {
            return true;
        }
        return false;
    }
    public function getField()
    {
        $dataField = [];
        if ($this->isGet()) {
            // xu ly lay du lieu voi phuong thuc get
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    if (is_array($value)) {
                        $dataField[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $dataField[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        if ($this->isPost()) {
            // xu ly lay du lieu voi phuong thuc post
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    if (is_array($value)) {
                        $dataField[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $dataField[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        return $dataField;
    }

    //validate set rules
    public function rules($rules = [])
    {
        $this->__rules = $rules;
    }
    // set message
    public function message($message)
    {
        $this->__message = $message;
    }
    // run validate
    public function validate()
    {
        $this->__rules = array_filter($this->__rules);
        $checkValidate = true;
        if (!empty($this->__rules)) {
            $dataField = $this->getField();
            foreach ($this->__rules as $fieldName => $ruleItem) {
                $ruleItemArr = explode('|', $ruleItem);
                foreach ($ruleItemArr as $rules) {
                    $ruleName = null;
                    $ruleValue = null;
                    $ruleArr = explode(':', $rules);
                    $ruleName = reset($ruleArr);
                    if (count($ruleArr) > 1) {
                        $ruleValue = end($ruleArr);
                    }
                    if ($ruleName == 'required') {
                        if (empty(trim($dataField[$fieldName]))) {
                            $this->setError($fieldName, $ruleName);
                            $checkValidate = false;
                        }
                    }
                    if ($ruleName == 'min') {
                        if (strlen(trim($dataField[$fieldName]) < $ruleValue)) {
                            $this->setError($fieldName, $ruleName);
                            $checkValidate = false;
                        }
                    }
                    if ($ruleName == 'max') {
                        if (strlen(trim($dataField[$fieldName])) > intval($ruleValue)) {
                            $this->setError($fieldName, $ruleName);
                            $checkValidate = false;
                        }
                    }
                    if ($ruleName == 'email') {
                        if (!filter_var($dataField[$fieldName], FILTER_VALIDATE_EMAIL)) {
                            $this->setError($fieldName, $ruleName);
                            $checkValidate = false;
                        }
                    }
                    if ($ruleName == 'match') {
                        if (trim($dataField[$fieldName]) != trim($dataField[$ruleValue])) {
                            $this->setError($fieldName, $ruleName);
                            $checkValidate = false;
                        }
                    }
                    if ($ruleName == 'unique') {
                        $tableName = null;
                        $fieldCheck = null;
                        if (!empty($ruleArr[1])) {
                            $tableName = $ruleArr[1];
                        }
                        if (!empty($ruleArr[2])) {
                            $fieldCheck = $ruleArr[2];
                        }
                        if (!empty($tableName) && !empty($fieldCheck)) {
                            if (count($ruleArr) == 3) {
                                $checkExist = $this->db->query("SELECT $fieldCheck FROM mywebsite.$tableName WHERE $fieldCheck='trim($dataField[$fieldName])'")->rowCount();
                            } elseif (count($ruleArr) == 4) {
                                if (!empty($ruleArr[3]) && preg_match('~.+?\=.+?~is', $ruleArr[3])) {
                                    $conditionWhere = $ruleArr[3];
                                    $conditionWhere = str_replace('=', '<>', $conditionWhere);
                                    $checkExist = $this->db->query("SELECT $fieldCheck FROM mywebsite.$tableName WHERE $fieldCheck='trim($dataField[$fieldName])' AND $conditionWhere")->rowCount();
                                }
                            }

                            if (!empty($checkExist)) {
                                $this->setError($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }
                    }
                    //callback validate age
                    if (preg_match('~^callback_(.+)~is', $ruleName, $callbackArr)) {
                        if (!empty($callbackArr[1])) {
                            $callbackName = $callbackArr[1];
                            $controller = App::$app->getCurrentController();
                            if (method_exists($controller, $callbackName)) {
                                $checkCallback = call_user_func_array([$controller, $callbackName], [trim($dataField[$fieldName])]);
                                if (!$checkCallback) {
                                    $this->setError($fieldName, $ruleName);
                                    $checkValidate = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        $sessionKey = Session::isInvalid();
        var_dump($sessionKey);
        Session::flashData($sessionKey . '_errors', $this->error());
        Session::flashData($sessionKey . '_old', $this->getField());
        return $checkValidate;
    }
    // get errors
    public function error($fieldName = '')
    {
        if (!empty($this->__errors)) {
            $errorArr = [];
            if (empty($fieldName)) {
                foreach ($this->__errors as $key => $error) {
                    $errorArr[$key] = reset($error);
                }
                return $errorArr;
            }
            return reset($this->__errors[$fieldName]);
        }
        return false;
    }
    // set errors
    public function setError($fieldName, $ruleName)
    {
        $this->__errors[$fieldName][$ruleName] = $this->__message[$fieldName . '.' . $ruleName];
    }

}
