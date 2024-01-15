<?php

/**
 * This manages all the authentication process.
 */

/** Loading all required files. */
require_once "config.php";
require_once "directory.php";
require_once "url.php";
require_once "function.php";
require_once "Database.php";
require_once "smtp.php";

class Authentication
{
    protected $db;
    public $error;
    public $output = array();

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getuserdata(INT $id = NULL, STRING $email = '', STRING $filter = '', STRING $unique_name = ''): bool
    {
        if ($id != NULL)
        {
            $run = $this->db->query("SELECT * FROM `user` WHERE `id` = ?", $id);

            if ($run->numRows())
            {
                $this->output = $run->fetchArray();
                return true;
            } else {
                return false;
            }
        } else if ($email != '') {
            $run = $this->db->query("SELECT * FROM `user` WHERE `email` = ?", $email);

            if ($run->numRows())
            {
                $this->output = $run->fetchArray();
                return true;
            } else {
                return false;
            }
        } else if ($filter != '') {
            $run = $this->db->query("SELECT * FROM `user` WHERE `filter` = ?", $filter);

            if ($run->numRows())
            {
                $this->output = $run->fetchArray();
                return true;
            } else {
                return false;
            }
        } else if ($unique_name != '') {
            $run = $this->db->query("SELECT * FROM `user` WHERE `unique_name` = ?", $unique_name);

            if ($run->numRows())
            {
                $this->output = $run->fetchArray();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function register(string $name, string $email, string $password): bool
    {
        if (!$this->db->query("SELECT * FROM `user` WHERE `email` = ?", $email)->numRows()) {
            $profile_img = "/assets/img/user/avatar-" . rand(1, 5) . ".png";
            if ($this->db->query("INSERT INTO `user`(`unique_name`, `name`, `email`, `password`, `status`, `profile_img`) VALUES (?,?,?,?,?,?)", array(strtolower(explode('@', trim($email))[0]), $name, $email, md5($password), '-1', $profile_img))) {
                $this->output['id'] = $this->db->lastInsertID();
                $this->output['temp_code'] = rand(100000, 999999);
                $this->output['temp_verify'] = rand_str(10);

                if ($this->db->query("INSERT INTO `user_temp` (user_id,temp_code,temp_verify) VALUES (?,?,?)", $this->output['id'], $this->output['temp_code'], $this->output['temp_verify'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $this->error = 104;
            return false;
        }
    }

    public function check_exist_and_forgot($email): bool
    {
        $run = $this->db->query("SELECT * FROM `user` WHERE `email` = ?", $email);
        if ($run->numRows())
        {
            $smtp = new simple_male_transfter_protocol();
            $user_data = $run->fetchArray();
            $this->output['id'] = $user_data['id'];

            $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ?", $user_data['id']);
            if ($run->numRows()) {
                $other_data = $run->fetchArray();

                $this->output['temp_verify'] = $other_data['temp_verify'];
                $this->output['temp_code'] = rand(100000, 999999);

                if ($this->db->query("UPDATE `user_temp` SET `temp_code` = ? WHERE `user_id` = ?", array($this->output['temp_code'], $user_data['id'])))
                {
                    $smtp->to = $email;
                    $smtp->subject = "OTP for password reset";
                    $smtp->message = "your OTP for password reset is " . $this->output['temp_code'];

                    if ($smtp->sent())
                    {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $this->output['temp_code'] = rand(100000, 999999);
                $this->output['temp_verify'] = rand_str(10);

                if ($this->db->query("INSERT INTO `user_temp` (user_id,temp_code,temp_verify) VALUES (?,?,?)", $run->fetchArray()['id'], $this->output['temp_code'], $this->output['temp_verify'])) {
                    $smtp->to = $email;
                    $smtp->subject = "OTP for password reset";
                    $smtp->message = "your OTP for password reset is " . $this->output['temp_code'];

                    if ($smtp->sent())
                    {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            $this->error = 105;
            return false;
        }
    }



    public function remove(INT $id): bool
    {
        if ($this->db->query("DELETE FROM `user_temp` WHERE `user_id` = ?", array($id))) {
            if ($this->db->query("DELETE FROM `user` WHERE `id` = ?", array($id))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function login (STRING $email, STRING $password): bool
    {
        $run = $this->db->query("SELECT * FROM `user` WHERE `email` = ? AND `password` = ?", array($email, md5($password)));
        if ($run->numRows())
        {
            $user_arr = $run->fetchArray();

            if ($user_arr['status'] == '-1') {
                $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ?", array($user_arr['id']));
                if ($run->numRows()) {
                    $user_arr = $run->fetchArray();
                    $filter = new filter();

                    if ($filter->set_verify_user($user_arr['id'], $user_arr['temp_verify'])) {
                        $temp_code = rand(100000, 999999);

                        if ($this->db->query("UPDATE `user_temp` SET `temp_code` = ? WHERE `user_id` = ?", array($temp_code, $_SESSION[SITE_NAME . '_TEMP_VERIFY_ID'])))
                        {
                            $smtp = new simple_male_transfter_protocol();
                            $smtp->to = $email;
                            $smtp->subject = 'User verification';
                            $smtp->message = "your One Time Password for " . SITE_NAME . " is " . $temp_code;

                            if ($smtp->sent())
                            {
                                $this->error = 102;
                            } else {
                                $filter->unset_verify_user();
                            }
                        }
                    }
                    return false;
                } else {
                    if ($this->db->query("DELETE FROM `user` WHERE `id` = ?", array($user_arr['id']))) {
                        $this->error = 101;
                        return false;
                    } else {
                        return false;
                    }
                }
            } elseif ($user_arr['status'] == '0') {
                $this->error = 103;
                return false;
            } else {
                if ($user_arr['status'] == '1') {
                    $filter = rand_str(10);
                    if ($this->db->query("UPDATE `user` SET `filter` = ? WHERE `email` = ?", $filter, $email)) {
                        $this->output['filter'] = $filter;
                        $this->output['email'] = $user_arr['email'];
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            $this->error = 101;
            return false;
        }
    }

    public function verify_otp(INT $otp, INT $id): bool
    {
        $run = $this->db->query("SELECT * FROM `user_temp` WHERE `user_id` = ? AND `temp_code` = ?", array($id, $otp));

        if ($run->numRows())
        {
            $filter = rand_str(10);

            if ($this->db->query("UPDATE `user` SET `filter` = ?, `status` = ? WHERE `id` = ?", array($filter, '1', $id)))
            {
                $run = $this->db->query("SELECT * FROM `user` WHERE `id` = ?", array($id));

                if ($run->numRows()) {
                    $user_arr = $run->fetchArray();

                    $this->output['email'] = $user_arr['email'];
                    $this->output['filter'] = $user_arr['filter'];

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function changePassword(INT $id, STRING $password): bool
    {
        if ($this->db->query("UPDATE `user` SET `password` = ? WHERE `id` = ?", array(md5($password), $id)))
        {
            return true;
        } else {
            return false;
        }

    }

    public function __destruct()
    {
        $this->db->close();
    }
}