<?php /** @noinspection ALL */

/**
 * This file used to create pages for admin panel.
 */

/** Loading all required files. */ 
require_once "config.php";
require_once "directory.php";
require_once "url.php";

/** This manages the page content. */
class page extends files_default
{
    private $url;
    private $directory;

    public function __construct(ARRAY $details, ARRAY $files = array(), STRING $body = '', BOOL $custom = false, ARRAY $user_data = array())
    {
        /** Getting all required things. */
        $this->url = new url();
        $this->directory = new dir();
        
        $title = ucwords(str_replace("-", " ", strtolower($details['title'])));

        /** HTML page starting. */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title><?=$title?> &lsaquo; <?=ucfirst(SITE_NAME)?></title>
    <link rel="shortcut icon" href="<?=$this->url->home() . LOGO_URL?>" type="image/x-icon">
<?php
        /** Including all the css file form 'files_default' class. */
        if (isset($this->css) && !empty($this->css)) {
            if (is_array($this->css)) {
                if (isset($this->css['title'])) {
                    $no_of_css_files = count($this->css) - 1;
?>
    <!--<?=$this->css['title']?>-->
<?php
                } else {
                    $no_of_css_files = count($this->css);
                }
        
                for ($i = 0; $i < $no_of_css_files; $i++) {
?>
    <link rel="stylesheet" href="<?=$this->url->home() . $this->css[$i]?>">
<?php
                }
            } else {
?>
    <link rel="stylesheet" href="<?=$this->url->home() . $this->css?>">
<?php
            }
        } 

        /** Including all the js files form the working page. */
        if (isset($files['css']) && !empty($files['css'])) {
?>
    <!--Page Specific And CSS Libraies-->
<?php
            if (is_array($files['css']) && count($files['css']) > 0) {
                for ($i = 0; $i < count($files['css']); $i++) {
?>
    <link rel="stylesheet" href="<?=$this->url->home() . $files['css'][$i]?>">
<?php
                }
            } else {
?>
    <link rel="stylesheet" href="<?=$this->url->home() . $files['css']?>">
<?php
            }
        }
/* Including all the custom css files form the working page. */
if (!empty($files['custom_css']))
{
    ?>
    <!--Custom CSS codes-->
    <?php
    if (is_array($files['custom_css']) && count($files['custom_css']) > 0)
    {
        for ($i = 0; $i < count($files['css']); $i++)
        {
            ?>
            <style>
                <?=$files['custom_css'][$i]?>
            </style>
            <?php
        }
    } else {
        ?>
        <style><?=$files['custom_css']?></style>
        <?php
    }
}
?>
</head>
<body>
<?php
        if (!$custom) {
?>
    <nav class="sticky">
        <a href="<?=$this->url->home()?>" class="logoname"><?=strtoupper(SITE_NAME)?></a>
        <div class="middlenav">
            <input type="text" name="search_friend_home" id="search_friend_home" placeholder="Search for friends" class="searchbar">
            <button class="searchbtn">Search</button>
        </div>
        <div class="rightnav dropdown-profile" style="flex-basis: 8%;">
            <img src="<?=$this->url->home() .$user_data['profile_img']?>" alt="profile-logo" class="logo1">
            <div class="dropdown-content">
                <a href="<?=$this->url->home()?>/auth.php?logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                <a href="<?=$this->url->home()?>/profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            </div>
        </div>
    </nav>
    <section class="page1">
        <div class="left1 box1">
            <div class="leftcont1<?=($this->url->clean_url($this->url->home() . '/profile.php') == $this->url->current_page() ? (!empty($_GET['user']) && $_GET['user'] == $user_data['unique_name'] || empty($_GET['user'])) ? ' active' : '' : '')?>">
                <img onclick="window.location.href='<?=$this->url->home() . '/profile.php'?>'" src="<?=$this->url->home() .$user_data['profile_img']?>" alt="profile-logo" class="logo">
                <div class="leftname">
                    <h3 onclick="window.location.href='<?=$this->url->home() . '/profile.php'?>'"><?=ucwords(strtolower(trim($user_data['name'])))?></h3>
                    <a href="javascript:void(0)" onclick="window.location.href='<?=$this->url->home() . '/profile.php'?>'">@<?=$user_data['unique_name']?></a>
                </div>
            </div>
            <div class="leftcont2">
<?php
        if (isset($this->ArrSidebar) && !empty($this->ArrSidebar) && is_array($this->ArrSidebar) && count($this->ArrSidebar) > 0) {
                foreach ($this->ArrSidebar as $title => $data) {
?>
                <div class="menu<?=(isset($data['link']) && !empty($data['link']) && $this->url->clean_url($this->url->home() . $data['link']) == $this->url->current_page() ? ' active' : '')?>" onclick="window.location.href='<?=$this->url->home() . $data['link']?>'">
                    <div class="icon11"> 
                        <img src="<?=(isset($data['link']) && !empty($data['link']) && $this->url->clean_url($this->url->home() . $data['link']) == $this->url->current_page() ? $this->url->home() . $data['active-icon'] : $this->url->home() . $data['icon'])?>">
                    </div>
                    <a href="javascript:void(0)" class="menuname"><?=$title?></a>
                </div>
<?php
                }
        }
?>
            </div>
        </div>
<?=$body?>

    </section>
<?php
        } else {
?>
<?=$body?>

<?php
        }

        /** Including all the js file form 'files_default' class. */
        if (isset($this->js) && !empty($this->js)) {
            if (is_array($this->js)) {
                if (isset($this->js['title'])) {
                    $no_of_js_files = count($this->js) - 1;
?>
    <!--<?=$this->js['title']?>-->
<?php
                } else {
                    $no_of_js_files = count($this->js);
                }

                for ($i = 0; $i < $no_of_js_files; $i++) {
?>
    <script src="<?=$this->url->home() . $this->js[$i]?>"></script>
<?php
                }
            } else {
?>
    <script src="<?=$this->url->home() . $this->js?>"></script>
<?php
            }
        }

        /** Including all the js files form the working page. */
        if (isset($files['js']) && !empty($files['js'])) {
?>
    <!--Page Specific And JS Libraies-->
<?php
            if (is_array($files['js']) && count($files['js']) > 0) {
                for ($i = 0; $i < count($files['js']); $i++) {
?>
    <script src="<?=$this->url->home() . $files['js'][$i]?>"></script>
<?php
                }
            } else {
?>
    <script src="<?=$this->url->home() . $files['js']?>"></script>
<?php
            }
        }

/** Including all the js files form the working page. */
if (isset($files['custom_js']) && !empty($files['custom_js'])) {
?>
    <!--custom-->
<?php
    if (is_array($files['custom_js']) && count($files['custom_js']) > 0) {
        for ($i = 0; $i < count($files['custom_js']); $i++) {
?>
    <script>
<?=$files['custom_js'][$i]?>
    </script>
<?php
        }
    } else {
?>
    <script>
<?=$files['custom_js']?>
    </script>

<?php
    }
}
?>
</body>
</html>
<?php
    }
}