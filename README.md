<h1 align= center>Sharechat</h1>
<h3 align = center>A social media web app</h3>
<p align="center">
<a href="https://www.php.net/"><img src="http://forthebadge.com/images/badges/made-with-php.svg" alt="made-with-php"></a>
<br>
    <img src="https://img.shields.io/github/stars/xtrimDev/Sharechat?style=for-the-badge&color=yellow" alt="Stars">
    <img src="https://img.shields.io/github/forks/xtrimDev/Sharechat?style=for-the-badge&color=green" alt="Forks">
    <img src="https://img.shields.io/github/watchers/xtrimDev/Sharechat?style=for-the-badge&color=yellow" alt="Watchers"> <br>
    <img src="https://img.shields.io/github/license/xtrimDev/Sharechat?style=for-the-badge&color=green" alt="License">
    <img src="https://img.shields.io/github/repo-size/xtrimDev/Sharechat?style=for-the-badge&color=yellow" alt="Repository Size">
    <img src="https://img.shields.io/github/contributors/xtrimDev/Sharechat?style=for-the-badge&color=green" alt="Contributors">
</p>  

## Description
Developed a dynamic social media web application allowing users to connect with friends, share posts, like content, engage in real-time chat, and save favorite posts. Features include an intuitive user interface, secure user authentication, and seamless interaction for enhanced social engagement. This project showcases full-stack development expertise and the ability to create a scalable, user-friendly platform.

## Deployment Methods

### 1. [Infinity free](https://www.infinityfree.com/)

## Database Configuration File
```path
  /sharechat.sql
```

## App Configuration
```path
  /includes/config.php
```
```php
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'YOUR APP NAME');
}

if (!defined('SITE_OWNER')) {
    define('SITE_OWNER', '@xtirmDev');
}

if (!defined('SITE_DESIGNER')) {
    define('SITE_DESIGNER', '@xtrimDev');
}

if (!defined('SITE_PUBLISHED_YEAR')) {
    define('SITE_PUBLISHED_YEAR', '2023');
}

if (!defined('LOGO_URL')) {
    define('LOGO_URL', '/assets/img/logo.png');
}

if (!defined('SMTP_EMAIL')) {
    define('SMTP_EMAIL', 'SMTP EMAIL');
}

if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', 'SMTP PASS');
}

if (!defined('DBHOST')) {
    define('DBHOST', 'YOUR MYSQL HOST');
}

if (!defined('DBUSER')) {
    define('DBUSER', 'YOUR MYSQL USER');
}

if (!defined('DBPASS')) {
    define('DBPASS', 'YOUR MYSQL PASSWORD');
}
if (!defined('DBNAME')) {
    define('DBNAME', 'YOUR MYSQL DB NAME');
}
```

## Features

- Live Chat
- Upload and see others posts
- Bookmark Posts
- Edit Profile

## Credits
[xtrimDev](https://github.com/xtrimDev/)
