<?php
/** Loading all required files. */
require_once __DIR__ . '/includes/load.php';

/** Checking the required filters. */
$filter = new filter();
$auth = new Authentication();
$url = new url();
$db = new Database();

if ($filter->check_authentication()) {
    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

    if (isset($_GET['load_post']))
    {
        $query = $db->query("SELECT * FROM `posts` WHERE `author` = ? ORDER BY `posts`.`date_of_publish` DESC", array($_GET['load_post']));

        $auth->getuserdata(trim($_GET['load_post']));
        if (!empty($auth->output['unique_name'])) {
            $name = $auth->output['unique_name'];
            $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

            if ($name == $auth->output['unique_name']) {
                $show = true;
            } else {
                $auth->getuserdata(trim($_GET['load_post']));
                $show = false;
            }
        } else {
            $show = false;
        }

        if ($query->numRows())
        {
            foreach ($query->fetchAll() as $rand => $data)
            {

                if ($show) {
                    $html = <<<EOPAGE
                    <div class="three-dots" style="background-color: black; padding: 0 2%;" onclick="delete_post({$data['id']});" data-delete-id="{$data['id']}">&#215;</div>
EOPAGE;
                } else {
                    $html = '';
                }
                echo <<<EOPAGE
                <div class="posts image-cont" id="post_{$data['id']}">
                    <img alt="post" src="{$url->home()}{$data['media_url']}">
                    {$html}
                </div>
EOPAGE;

            }
        }
        die();
    }

    if (!empty($_GET['del_post']))
    {
        $query = $db->query("DELETE FROM `posts` WHERE `author` = ? AND `id` = ?", array($auth->output['id'],$_GET['del_post']));

        if ($query)
        {
            echo "success";
        } else {
            echo "something went wrong!";
        }

        die();
    }

    if (isset($_GET['update']) && isset($_FILES['fileImg'])) {
        if ($_FILES['fileImg']['error'] == UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES['fileImg']['name'], PATHINFO_EXTENSION);

            $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($fileExtension), $allowedImageExtensions)) {
                $filetype = "image";
            } else {
                $filetype = false;
            }

            if ($filetype == "image") {
                $dist = '/uploads/profile/';
                $file_name = generateFileName(basename($_FILES["fileImg"]["name"]));

                $target_file = __DIR__ . $dist . $file_name;
                if (move_uploaded_file($_FILES["fileImg"]["tmp_name"], $target_file)) {
                    if ($db->query("UPDATE `user` SET `profile_img` = ? WHERE `id` = ?", array($dist . $file_name, $auth->output['id']))) {
                        echo "success";
                    } else {
                        echo "Something went wrong!";
                    }
                } else {
                    echo "Something went wrong!";
                }
            } else {
                echo "The <code>.{$fileExtension}</code> file is not allowed";
            }
        } else {
            echo 'File upload failed with error code ' . $_FILES['fileImg']['error'];
        }
        die();
    }

    /** Details configuration for the page. */
    $details = array(
        'title' => 'Profile'
    );

    if (!empty($_GET['user'])) {
        $auth->getuserdata(NULL, '', '', $_GET['user']);
        $author = $auth->output['id'];
    } else {
        $author = $auth->output['id'];
    }

    /** Required files for the page. */
    $files = array(
        'css' => '/assets/css/profile.css',
        'js' => '/assets/js/jquery.min.js',
        'custom_js' => [
            <<<EOPAGE
    document.getElementById("fileImg").onchange = function(){
        document.getElementById("image").src = URL.createObjectURL(fileImg.files[0]); // Preview new image

        document.getElementById("cancel").style.display = "block";
        document.getElementById("confirm").style.display = "block";

        document.getElementById("upload").style.display = "none";
    }

    var userImage = document.getElementById('image').src;
    document.getElementById("cancel").onclick = function(){
        document.getElementById("image").src = userImage; // Back to previous image

        document.getElementById("cancel").style.display = "none";
        document.getElementById("confirm").style.display = "none";

        document.getElementById("upload").style.display = "block";
    }
EOPAGE,
            <<<EOPAGE
    $(document).ready(function() {
        $("#update_img").click(function() {
            // Create a FormData object to store the form data
            var formData = new FormData($("#form")[0]);

            // Make an AJAX request
            $.ajax({
                url: 'profile.php?update',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response === 'success') {
                        swal({
                            title: "Uploaded",
                            text: "Profile picture changed",
                            icon: "success",
                        }).then((value) => {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Failed!",
                            text: response,
                            icon: "danger",
                        });
                    }
                }
            });
        });
    });
    
    $(document).ready(function(){
        // Execute the AJAX call on document load
        $.ajax({
            url: 'profile.php?load_post={$author}', // replace with the actual path to your PHP script
            method: 'GET', // or 'POST' depending on your needs
            dataType: 'html', // change to 'json' if your PHP script returns JSON
            success: function(response) {
                if (response === '') 
                {
                    $('.Posts-grid').html('<div id="msg_post" style="width: 100%; text-align:center;"><span style="background: transparent; font-size: 20px; margin-bottom: 5px;">No Data Found</span></div>');
                }else {
                    // Handle the successful response from the server
                    $('.Posts-grid').html(response);
                }
            }
        });
    });
    function delete_post(PostId){
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this post!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'profile.php?del_post='+PostId, // replace with the actual path to your PHP script
                    method: 'GET', // or 'POST' depending on your needs
                    dataType: 'html', // change to 'json' if your PHP script returns JSON
                    success: function(response) {
                        if (response === 'success') 
                        {
                            $('#post_'+PostId).remove();
                        } else {
                            swal({
                                title: "Failed!",
                                text: response,
                                icon: "danger",
                            });
                        }
                    }
                });
            }
          });
    }
EOPAGE
        ]
    );

    $unique_name = $auth->output['unique_name'];
    $auth->output = [];

    if (!empty($_GET['user'])) {
        $auth->getuserdata(NULL, '', '', trim($_GET['user']));

        if (!empty($auth->output['unique_name'])) {
            $name = $auth->output['unique_name'];
            $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

            if ($name == $auth->output['unique_name']) {
                $show = true;
            } else {
                $auth->getuserdata(NULL, '', '', trim($_GET['user']));
                $show = false;
            }
        } else {
            $show = false;
        }
    } else {
        $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);
        $show = true;
    }

    if ($auth->output == [])
    {
        $found = false;
    } else {
        $found = true;
    }

    if ($found)
    {
        $profile_img = $url->home() .$auth->output['profile_img'];

        if ($auth->output['status'] == -1) {
            $status = "Not verified.";
        } elseif ($auth->output['status'] == 0) {
            $status = "Account is banned.";
        } else {
            $status = "Active";
        }

        if ($show) {
            $html = <<<EOPAGE
                        <div class="rightRound" id="upload" style="display: block;">
                            <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="leftRound" id="cancel" style="display: none;">
                            <i class="fa fa-times"></i>
                        </div>
                        <div class="rightRound" id="confirm" style="display: none;">
                            <input type="submit" id="update_img" name="update_img">
                            <i class="fa fa-check"></i>
                        </div>
EOPAGE;

        } else {
            $html = <<<EOPAGE
EOPAGE;
        }

        $qry = $db->query("SELECT * FROM `posts` WHERE `author` = ?", array($auth->output['id']));
        $no_of_post = $qry->numRows();

        $qry = $db->query("SELECT * FROM `frind_req` WHERE (`from` = ? AND `status` = '1') OR (`to` = ? AND `status` = '1')", array($auth->output['id'], $auth->output['id']));
        $no_of_friends = $qry->numRows();

        $name = ucwords($auth->output['name']);
        /** Body of the page. */
        $body = <<<EOPAGE
        <div class="middle1 box1">
            <div class="profilecard">
                <form class="form" id="form" onsubmit="return false;" enctype="multipart/form-data" method="post">
                    <div class="upload">
                        <img src="{$profile_img}" id="image">
                        {$html}
                    </div>
                </form>
                <div class="profileinfo">
                    <span>{$name}</span>
                    <span>@{$auth->output['unique_name']}</span>
                </div>
                <div class="followerinfo">
                    <div class="following">
                        <span>{$no_of_friends}</span>
                        <span>Friends</span>
                    </div>
                    <div class="vl"></div>
                    <div class="mail">
                        <span>E-mail</span>
                        <span>{$auth->output['email']}</span>
                    </div>
                    <div class="vl"></div>
                    <div class="post">
                        <span>{$no_of_post}</span>
                        <span>Posts</span>
                    </div>
                </div>
            </div>
            <div class="postcard">
                <h1>Posts</h1>
                <div class="Posts-grid"> </div>
            </div>
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
EOPAGE;
    } else {
        $body = <<<EOPAGE
        <div class="middle1 box1">
            <div id="msg_post" style="text-align:center;"><span style="background: transparent; color: white; font-size: 20px; margin-bottom: 5px;">No Data Found</span></div>
        </div>
EOPAGE;

    }

    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);
    /** Creating the page. */
    new page($details, $files, $body, false,  $auth->output);
} else {
    /** Goto login page */
    goto_login();
}