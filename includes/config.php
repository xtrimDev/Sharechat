<?php

/**
 * @Note : The url of the all css, js and the logo files used form the home of the site.
 * This file configure the site, and it's related data.
 */

/** These all are the site details. */
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'ShareChat');
}

if (!defined('SITE_OWNER')) {
    define('SITE_OWNER', 'WebDev Community');
}

if (!defined('SITE_DESIGNER')) {
    define('SITE_DESIGNER', 'WebDev Community');
}

if (!defined('SITE_PUBLISHED_YEAR')) {
    define('SITE_PUBLISHED_YEAR', '2023');
}

if (!defined('LOGO_URL')) {
    define('LOGO_URL', '/assets/img/logo.png');
}

if (!defined('SMTP_EMAIL')) {
    define('SMTP_EMAIL', 'bhandarisameer512@gmail.com');
}

if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', 'shuqarukimqsjucq');
}

if (!defined('DBHOST')) {
    define('DBHOST', 'localhost');
}

if (!defined('DBUSER')) {
    define('DBUSER', 'root');
}

if (!defined('DBPASS')) {
    define('DBPASS', '');
}
if (!defined('DBNAME')) {
    define('DBNAME', 'sharechat');
}

/** This class manage all default files for the admin panel. */
class files_default
{    
    /**  This is the sidebar Array. */
    protected $ArrSidebar = array(
        'Home' => [
            'link' => '/index.php',
            'icon' => '/assets/vectors/home.svg',
            'active-icon' => '/assets/vectors/home-fill.svg'
        ],
        'Saved' => [
            'link' => '/saved.php',
            'icon' => '/assets/vectors/saved.svg',
            'active-icon' => '/assets/vectors/saved-fill.svg'
        ],
        'Friends' => [
            'link' => '/friends.php',
            'icon' => '/assets/vectors/friend-2.svg',
            'active-icon' => '/assets/vectors/friend.svg',
        ],
        'Message' => [
            'link' => '/message.php',
            'icon' => '/assets/vectors/message.svg',
            'active-icon' => '/assets/vectors/message-fill.svg'
        ]
    );

    /** This css files will automatically include all the pages. */
    protected $css = array(
        '/assets/css/homestyle.css'
    );
    
    /** This js files will automatically include all the pages. */
    protected $js = array(
        '/assets/js/jquery.min.js',
        '/assets/js/home.js'
    );
}