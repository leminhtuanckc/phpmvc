<?php
class Session
{
    // data (key, value) => get session
    //  data (key) => get session
    public static function data($key = '', $value = '')
    {
        $sessionKey = self::isInvalid();
        if (!empty($value)) {
            if (!empty($key)) {
                $_SESSION[$sessionKey][$key] = $value; // set session
                return true;
            }
            return false;
        } else {
            if (empty($key)) {
                if (isset($_SESSION[$sessionKey])) {
                    return $_SESSION[$sessionKey];
                }
            } else {
                if (isset($_SESSION[$sessionKey][$key])) {
                    return $_SESSION[$sessionKey][$key]; // get session
                }
            }
        }
    }
    // delete(key) => xoa session voi key
    // delete() => xoa het session
    public static function delete($key = '')
    {
        $sessionKey = self::isInvalid();
        if (!empty($key)) {
            if (isset($_SESSION[$sessionKey][$key])) {
                unset($_SESSION[$sessionKey][$key]);
                return true;
            }
            return false;
        } else {
            unset($_SESSION[$sessionKey]);
            return true;
        }
        return false;
    }
    /*
    flash data
    set flash data => giong nhu set session
    get flash data => giong nhu get session, xoa luon session sau khi get
     */

    public static function flashData($key = '', $value = '')
    {
        $dataFlash = self::data($key, $value);
        if (empty($value)) {
            self::delete($key);
        }
        return $dataFlash;
    }

    public static function showErrors($message)
    {
        $data = ['message' => $message];
        App::$app->loadError('exception', $data);
        die();
    }
    public static function isInvalid()
    {
        global $config;
        if (!empty($config['session'])) {
            $session_config = $config['session'];
            if (!empty($session_config['session_key'])) {
                $sessionKey = $session_config['session_key'];
                return $sessionKey;
            } else {
                self::showErrors('Thieu cau hinh session xin vui long kiem tra lai');
            }
        } else {
            self::showErrors('Thieu cau hinh session xin vui long kiem tra');
        }
    }
}
