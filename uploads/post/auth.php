<?php
/** Loading all required files. */
require_once __DIR__ . '/includes/load.php';

/** Checking the required filters. */
$filter = new filter();

if (!$filter->check_authentication())
{
    $url = new url();
    $auth = new Authentication();
    $smtp = new simple_male_transfter_protocol();
    $db = new Database();

    if ($filter->check_verify_user_status()) {
        if (isset($_GET['verify'])) {
            $msg = '';
            if (isset($_GET['resend']) && $_GET['resend'] == 'verify')
            {
                $run = $db->query("SELECT * FROM `user_temp` WHERE `user_id` = ?", array($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']));

                $user_data = $run->fetchArray();
                $smtp->to = $db->query("SELECT * FROM `user` WHERE `id` = ?", array($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']))->fetchArray()['email'];

                $smtp->subject = 'OTP for password reset';
                $smtp->message = "your OTP for password reset is " . $user_data['temp_code'];

                if ($smtp->sent()) {
                    $msg = true;
                } else {
                    $msg = false;
                }
            }

            /** Details configuration for the page. */
            $details = array(
                'title' => 'Verification'
            );

            /** Required files for the page. */
            $files = array(
                'css' => [
                    '/assets/css/otpstyle.css'
                ],
                'js' => [
                    '/assets/js/jquery.min.js',
                    '/assets/js/validation.min.js',
                    '/assets/js/otpscript.js'
                ]
            );

            $run = $db->query("SELECT * FROM `user` WHERE `id` = ?", array($_SESSION[SITE_NAME . '_TEMP_VERIFY_ID']));

            $user_data = $run->fetchArray();
            $user_email = partiallyhideEmailAddress($user_data['email']);

            if (!empty($msg) && $msg != '' && $msg) {
                $error = <<<EOPAGE

            <div class="msg_danger" style="background: #1c7430; color: white;"> <i class="fa-solid fa-circle-info"></i> &nbsp; Code Sent Successfully!</div>
        
EOPAGE;
            } else if (!empty($msg) && $msg != '' && !$msg) {
                $error = <<<EOPAGE

            <div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; Something Went Wrong!</div>
        
EOPAGE;
            } else {
                $error = '';
            }

            /** Body of the page. */
            $body = <<<EOPAGE
    <form id="verify_otp" onsubmit="return false" class="container_otp">
        <h1>OTP Verification</h1>
        <p>Code has been sent to {$user_email}</p>
        <div id="error_msg" style="margin-top: 30px;">{$error}</div>
        <div class="code-container">
            <input type="number" name="int1" id="int1" class="code" min="0" max="9">
            <input type="number" name="int2" id="int2" class="code" min="0" max="9">
            <input type="number" name="int3" id="int3" class="code" min="0" max="9">
            <input type="number" name="int4" id="int4" class="code" min="0" max="9">
            <input type="number" name="int5" id="int5" class="code" min="0" max="9">
            <input type="number" name="int6" id="int6" class="code" min="0" max="9">
        </div>
        <div>
            <button type="submit" id="otp_verify" name="otp_verify" class="btn btn-primary">Verify</button>
        </div>
        <small>
            Didn't receive the Code? <strong><a href="{$url->current()}&resend=verify">Resend</a></strong>
        </small>
    </form>
EOPAGE;

            /** Creating the page. */
            new page($details, $files, $body, true);
            die();
        } else {
            $filter->unset_verify_user();
        }
    }

    if (isset($_GET['reset']) && $filter->check_reset_password())
    {
        /** Details configuration for the page. */
        $details = array(
            'title' => 'Set New Password.'
        );

        /** Required files for the page. */
        $files = array(
            'css' => [
                '/assets/modules/bootstrap/css/bootstrap.min.css',
                '/assets/css/forgotstyle.css',
            ],
            'js' => [
                '/assets/js/jquery.min.js',
                '/assets/js/validation.min.js',
                '/assets/js/resetscript.js'
            ]
        );

        /** Body of the page. */
        $body = <<<EOPAGE
    <div class="forgot_container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form id="reset_pass" method="POST" onsubmit="return false">
                    <h2 class="text-center">New Password</h2>
                    <p class="text-center">
                        Please create a new password that
                        <br>you don't use on any other site
                    </p>
                    <div id="error_msg"></div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="password" name="password" placeholder="Create new password">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="_password" name="_password" placeholder="Confirm your password">
                    </div>
                    <div class="form-group">
                        <button class="form-control button" type="submit" id="change_password" name="change_password">Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
EOPAGE;

        /** Creating the page. */
        new page($details, $files, $body, true);
        die();
    } else {
        $filter->unset_reset_password();
    }

    if (isset($_GET['forgot']))
    {
        if ($_GET['forgot'] == 'match' && $filter->check_forgot_user()) {
            $msg = '';
            if (isset($_GET['resend']) && $_GET['resend'] == 'forgot')
            {
                $run = $db->query("SELECT * FROM `user_temp` WHERE `user_id` = ?", array($_SESSION[SITE_NAME . '_FORGOT_TEMP_VERIFY_ID']));

                $user_data = $run->fetchArray();
                $smtp->to = $_SESSION[SITE_NAME . '_FORGOT_USER'];

                $smtp->subject = 'OTP for password reset';
                $smtp->message = "your OTP for password reset is " . $user_data['temp_code'];

                if ($smtp->sent()) {
                    $msg = true;
                } else {
                    $msg = false;
                }
            }

            $user_email = partiallyhideEmailAddress($_SESSION[SITE_NAME . '_FORGOT_USER']);

            /** Details configuration for the page. */
            $details = array(
                'title' => 'Verification'
            );

            /** Required files for the page. */
            $files = array(
                'css' => [
                    '/assets/css/otpstyle.css'
                ],
                'js' => [
                    '/assets/js/jquery.min.js',
                    '/assets/js/validation.min.js',
                    '/assets/js/otpscript.js'
                ]
            );

            if (!empty($msg) && $msg != '' && $msg) {
                $error = <<<EOPAGE

            <div class="msg_danger" style="background: #1c7430; color: white;"> <i class="fa-solid fa-circle-info"></i> &nbsp; Code Sent Successfully!</div>
        
EOPAGE;
            } else if (!empty($msg) && $msg != '' && !$msg) {
                $error = <<<EOPAGE

            <div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; Something Went Wrong!</div>
        
EOPAGE;
            } else {
                $error = '';
            }

            /** Body of the page. */
            $body = <<<EOPAGE
    <form id="verify_otp_forgot" onsubmit="return false" class="container_otp">
        <h1>OTP Verification</h1>
        <p>Code has been sent to {$user_email}</p>
        <div id="error_msg" style="margin-top: 30px;">{$error}</div>
        <div class="code-container">
            <input type="number" name="int1" id="int1" class="code" min="0" max="9">
            <input type="number" name="int2" id="int2" class="code" min="0" max="9">
            <input type="number" name="int3" id="int3" class="code" min="0" max="9">
            <input type="number" name="int4" id="int4" class="code" min="0" max="9">
            <input type="number" name="int5" id="int5" class="code" min="0" max="9">
            <input type="number" name="int6" id="int6" class="code" min="0" max="9">
        </div>
        <div>
            <button type="submit" id="otp_verify_forgot" name="otp_verify_forgot" class="btn btn-primary">Verify</button>
        </div>
        <small>
            Didn't receive the Code? <strong><a href="{$url->current()}&resend=forgot">Resend</a></strong>
        </small>
    </form>
EOPAGE;

            /** Creating the page. */
            new page($details, $files, $body, true);
            die();
        } else {
            $filter->unset_forgot_user();
        }

        /** Details configuration for the page. */
        $details = array(
            'title' => 'Forgot Password'
        );

        /** Required files for the page. */
        $files = array(
            'css' => [
                '/assets/modules/bootstrap/css/bootstrap.min.css',
                '/assets/css/forgotstyle.css',
            ],
            'js' => [
                '/assets/js/jquery.min.js',
                '/assets/js/validation.min.js',
                '/assets/js/forgotscript.js'
            ]
        );

        /** Body of the page. */
        $body = <<<EOPAGE
    <div class="forgot_container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form id="forgot_pass" method="POST" onsubmit="return false">
                    <h2 class="text-center">Forgot Password?</h2>
                    <p class="text-center">Enter your email address to reset your password.</p>
                    <div id="error_msg"></div>
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <button class="form-control button" type="submit" name="check" id="check">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
EOPAGE;

        /** Creating the page. */
        new page($details, $files, $body, true);
        die();
    } else {
        $filter->unset_forgot_user();
    }


    /** Details configuration for the page. */
    $details = array(
        'title' => 'Authentication'
    );

    /** Required files for the page. */
    $files = array(
        'css' => [
            '/assets/css/loginstyle.css'
        ],
        'js' => [
            '/assets/js/jquery.min.js',
            '/assets/js/validation.min.js',
            '/assets/js/authentication.js'
        ]
    );

    $add =  (isset($_GET['signup']) ? ' active' : '');

    /** Body of the page. */
    $body = <<<EOPAGE
    <div class="container{$add}" id="container">
        <div class="form-container sign-up">
            <form id="RegistrationForm" onsubmit="return false">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <div id="error_reg_msg"></div>
                <span>or use your email for registration</span>
                <input type="text" id="name" name="name" placeholder="Name" autocomplete="off">
                <input type="email" id="email" name="email" placeholder="Email" autocomplete="off">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off">
                <button type="submit" name="sign_up" id="sign_up">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form id="LoginForm" onsubmit="return false">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <div id="error_msg"></div>
                <span>or use your email password</span>
                <input type="email" id="_email" name="_email" placeholder="Email" autocomplete="off">
                <input type="password" id="_password" name="_password" placeholder="Password" autocomplete="off">
                <a href="{$url->current_page()}?forgot">Forget Your Password?</a>
                <button name="sign_in" id="sign_in" type="submit">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
             <div class="toggle"> 
                <div class="toggle-panel toggle-left">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details to use all site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome Back!</h1>
                    <p>Register with your personal details to use all site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
EOPAGE;

    /** Creating the page. */
    new page($details, $files, $body, true);
} elseif(isset($_GET['logout'])) {
    $filter->unset_authentication();
    goto_login();
}else {
    /** Goto home page */
    goto_home();
}