<?php

/**
 * This file used to make filter to protect files from unused access.
 */

/** Loading all required files. */
require_once "config.php";
require_once "directory.php";
require_once "url.php";
require_once "Database.php";

class filter
{
    private $db;
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
        $this->db = new Database();
    }
    public function set_authentication(STRING $email, STRING $filter): bool
    {
        if (setcookie(SITE_NAME . '_AUTH_FILTER_FOR', $email, time() + (86400 * 30), "/") && setcookie(SITE_NAME . '_AUTH_FILTER_VALUE', $filter, time() + (86400 * 30), "/")) {
            return true;
        } else {
            return false;
        }
    }
    public function unset_authentication(): bool
    {
        if (setcookie(SITE_NAME . '_AUTH_FILTER_FOR', '', time() - 3600) && setcookie(SITE_NAME . '_AUTH_FILTER_VALUE', '', time() - 3600)) {
            return true;
        } else {
            return false;
        }
    }

    public function check_authentication(): bool
    {
        if (isset($_COOKIE[SITE_NAME . '_AUTH_FILTER_FOR']) && isset($_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']))
        {
            if ($this->db->query("SELECT * FROM `user` WHERE `email` = ? AND `filter` = ? AND `status` = ?", array($_COOKIE[SITE_NAME . '_AUTH_FILTER_FOR']), $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE'], '1')->numRows()) {
                return true;
            } else {
                $this->unset_authentication();
                return false;
            }
        } else {
            return false;
        }
    }

    public function set_verify_user($id, $temp_verify): bool
    {
        $_SESSION[SITE_NAME . '_TEMP_VERIFY'] = $temp_verify;
        $_SESSION[SITE_NAME . '_TEMP_VERIFY_ID'] = $id;

        return true;
    }

    public function check_verify_user_status(): bool
    {
        if (isset($_SESSION[SITE_NAME . '_TEMP_VERIFY']) && isset($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID'])) {
            $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ?", array($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']));
            if ($run->numRows()) {
                $user_data = $run->fetchArray();

                if ($user_data['temp_verify'] == $_SESSION[SITE_NAME . '_TEMP_VERIFY']) {
                    $run = $this->db->query("SELECT * FROM `user` WHERE `id` = ?", array($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']));
                    if ($run->numRows()) {
                        $user_data = $run->fetchArray();
                        if ($user_data['status'] == '-1') {
                            return true;
                        } else {
                            $this->unset_verify_user();
                            return false;
                        }
                    } else {
                        $this->unset_verify_user();
                        return false;
                    }
                } else {
                    $this->unset_verify_user();
                    return false;
                }
            } else {
                $this->unset_verify_user();
                return false;
            }
        } else {
            return false;
        }
    }

    public function unset_verify_user(): bool
    {
        unset($_SESSION[SITE_NAME . '_TEMP_VERIFY']);
        unset($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']);
        return true;
    }

    public function set_forgot_user(STRING $email, INT $id, STRING $temp_verify): bool
    {
        $_SESSION[SITE_NAME . '_FORGOT_USER'] = $email;
        $_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID'] = $id;
        $_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY'] = $temp_verify;

        return true;
    }

    public function check_forgot_user():bool
    {
        if (isset($_SESSION[SITE_NAME . '_FORGOT_USER']) && isset($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID']) && isset($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY'])) {
            $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ? AND `temp_verify` = ?", array($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID'], $_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY']));
            if ($run->numRows()) {
                if ($this->db->query("SELECT * FROM `user` WHERE `id` = ? AND `email` = ?", array($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID'], $_SESSION[SITE_NAME . '_FORGOT_USER']))->numRows()) {
                    return true;
                } else {
                    $this->unset_forgot_user();
                    return false;
                }
            } else {
                $this->unset_forgot_user();
                return false;
            }
        } else {
            return false;
        }
    }


    public function unset_forgot_user():bool
    {
        unset($_SESSION[SITE_NAME . '_FORGOT_USER']);
        unset($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID']);
        unset($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY']);
        return true;
    }

    public function set_reset_password(INT $id, STRING $temp_verify): bool
    {
        $_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY_ID'] = $id;
        $_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY'] = $temp_verify;

        return true;
    }

    public function check_reset_password():bool
    {
        if (isset($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY_ID']) && isset($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY'])) {
            $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ? AND `temp_verify` = ?", array($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY_ID'], $_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY']));
            if ($run->numRows())
            {
                if ($this->db->query("SELECT * FROM `user` WHERE `id` = ?", array($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY_ID']))->numRows())
                {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->unset_reset_password();
                return false;
            }
        } else {
            return false;
        }
    }

    public function unset_reset_password():bool
    {
        unset($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY_ID']);
        unset($_SESSION[SITE_NAME . '_SET_PASSWORD_TEMP_VERIFY']);
        return true;
    }

    public function __destruct()
    {
        $this->db->close();
    }
}