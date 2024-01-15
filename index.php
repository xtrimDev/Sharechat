<?php
/** Loading all required files. */
require_once __DIR__ . '/includes/load.php';

$filter = new filter();
$url = new url();
$auth = new Authentication();
$db = new Database();

/** Checking the required filters. */
if ($filter->check_authentication()) {
    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

    if (!empty($_GET['search_key']))
    {
        $query = $db->query("SELECT * FROM `user` WHERE (`name` LIKE '%" . trim($_GET['search_key']) . "%' AND `id` <> " . $auth->output['id'] . ") OR (`unique_name` LIKE '%" . trim($_GET['search_key']) . "%' AND `id` <> " . $auth->output['id'] . ")");
        $body = <<<EOPAGE
            <div class="middle1 ">
                <div class="pumk"> 
                    <h1 style="color: white">Search result for {$_GET['search_key']}</h1>             
EOPAGE;
        if ($query->numRows())
        {
            foreach ($query->fetchAll() as $rand => $data)
            {
                $body .= <<<EOPAGE
                    <div class="reqbox" style=" margin-top: 20px;">
                    
                        <div class="reqimg2">
                            <img alt="user" src="{$data['profile_img']}">
                        </div>
                        <div class="reqbox2">
                            <div class="msg222">
                                <a class="msgername" href="{$url->home()}/profile.php?user={$data['unique_name']}">{$data['name']}</a>
                                <a class="msgby" href="{$url->home()}/profile.php?user={$data['unique_name']}">@{$data['unique_name']}</a>
                            </div>
                            <div class="reqbtns">
                                <button class="accept add_friend" data-user-id="{$data['id']}">Add friend</button>
                            </div>
                        </div>
                    </div>
EOPAGE;

            }
        }
        $body .= <<<EOPAGE
                </div>
            </div>
EOPAGE;

        new page(array('title' => 'Search'), array(), $body, false,  $auth->output);
        die();
    }

    if (isset($_GET['save']) && isset($_POST['postId']))
    {
        if ($db->query("SELECT * FROM `user_save` WHERE `post_id` = ? AND `user_id` = ?", array(trim($_POST['postId']), $auth->output['id']))->numRows())
        {
            if ($db->query("DELETE FROM `user_save` WHERE `post_id` = ? AND `user_id` = ?", array(trim($_POST['postId']), $auth->output['id'])))
            {
                //if already liked
                echo "unsaved";
            } else {
                echo "Something went wrong!";
            }

        } else {
            //if not liked
            if ($db->query("INSERT INTO `user_save`(`post_id`, `user_id`) VALUES (?, ?)", array(trim($_POST['postId']), $auth->output['id'])))
            {
                echo "saved";
            } else {
                echo "Something went wrong!";
            }
        }

        die();
    }

    if (isset($_GET['like']) && isset($_POST['postId']))
    {
        if ($db->query("SELECT * FROM `post_data` WHERE `post_id` = ? AND `user_id` = ?", array(trim($_POST['postId']), $auth->output['id']))->numRows())
       {
           if ($db->query("DELETE FROM `post_data` WHERE `post_id` = ? AND `user_id` = ?", array(trim($_POST['postId']), $auth->output['id'])))
           {
               //if already liked
               echo "unlike";
           } else {
               echo "Something went wrong!";
           }

       } else {
            //if not liked
            if ($db->query("INSERT INTO `post_data`(`post_id`, `user_id`) VALUES (?, ?)", array(trim($_POST['postId']), $auth->output['id'])))
            {
                echo "like";
            } else {
                echo "Something went wrong!";
            }
        }

        die();
    }

    if (isset($_GET['load']) && isset($_POST["limit"]) && isset($_POST["start"]))
    {
        $run = $db->query("SELECT * FROM `posts` WHERE 1 ORDER BY `posts`.`date_of_publish` DESC LIMIT ?, ?", array(trim($_POST['start']), trim($_POST['limit'])));

        if ($run->numRows())
        {
            foreach ($run->fetchAll() as $rand => $post)
            {
                $caption = $post['caption'];

                $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

                $query = $db->query("SELECT * FROM `post_data` WHERE `post_id` = ? AND `user_id` = ?", array($post['id'], $auth->output['id']));
                if ($query->numRows())
                {
                    $class_like = 'fa-solid';
                    $style_like = ' style="color: red;"';
                } else {
                    $class_like = 'fa-regular';
                    $style_like = '';
                }
                $run = $db->query("SELECT * FROM `post_data` WHERE `post_id` = ?", array($post['id']));
                $like_count = $run->numRows();

                $query = $db->query("SELECT * FROM `user_save` WHERE `post_id` = ? AND `user_id` = ?", array($post['id'], $auth->output['id']));
                if ($query->numRows())
                {
                    $class_save = 'fa-solid';
                } else {
                    $class_save = 'fa-regular';
                }

                if ($post['media_type'] == 'image')
                {
                    $media_file = <<<EOPAGE
                    <img class="cardimg" src="{$url->home()}{$post['media_url']}" alt="error loading image">
EOPAGE;
                } else {
                    $media_file = <<<EOPAGE
                    <video  class="cardimg" controls>
                        <source src="{$url->home()}{$post['media_url']}">
                        Your browser does not support the video tag.
                    </video>
                  
EOPAGE;
                }

                $auth->getuserdata($post['author']);
                $profile_img = $url->home() .$auth->output['profile_img'];
                echo <<<EOPAGE
            <div class="card1" data-post-id="{$post['id']}">
                <div class="cardbox">
                    <div class="cardtop">
                        <img onclick="window.location.href='{$url->home()}/profile.php?user={$auth->output['unique_name']}'" class="friendsimg" src="{$profile_img}" alt="error">
                        <div>
                            <h2 class="name" style="font-size: 20px; text-transform: capitalize;" onclick="window.location.href='{$url->home()}/profile.php?user={$auth->output['unique_name']}'">{$auth->output['name']}</h2>
                            <span class="date">{$post['date_of_publish']}</span>
                        </div>
                    </div>
                    {$media_file}
                    <div class="cardsubtitle">{$caption}</div>
                    <hr>
                    <div class="icons">
                        <div>
                            <div style="align-items:center; display: flex; flex-direction: column; gap: 0 !important;">
                                <div>
                                    <i class="likeicon {$class_like} fa-heart like-btn"{$style_like} data-post-id="{$post['id']}"></i>
                                </div>
                                <div class="likescount" style="gap: 0; display: flex; margin: 0; align-items: center;">
                                    <span id="like_count_{$post['id']}">{$like_count}</span> &nbsp; Likes
                                </div>
                            </div>
                            <i class="shareicon fa-solid fa-share"></i>
                        </div>
                        <div>
                            <i class="saveicon {$class_save} fa-bookmark save-btn" style="float: right" data-post-id="{$post['id']}"></i>
                        </div>
                    </div>
                    
                </div>
            </div>
EOPAGE;
            }
        }
        die();
    }

    if (isset($_GET['save_post']) && isset($_FILES['post_file']) && isset($_POST['post_caption']))
    {
        if ($_FILES['post_file']['error'][0] == UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES['post_file']['name'][0], PATHINFO_EXTENSION);

            // Define an array of allowed video and image file extensions
            $allowedVideoExtensions = ['mp4', 'avi', 'mkv', '3gp'];
            $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Check if the file extension is in the allowed video extensions array
            if (in_array(strtolower($fileExtension), $allowedVideoExtensions)) {
                $filetype = "video";
            } elseif (in_array(strtolower($fileExtension), $allowedImageExtensions)) {
                $filetype = "image";
            } else {
                $filetype =  false;
            }

            if ($filetype == 'video' || $filetype == 'image')
            {
                $dist = '/uploads/post/';
                $file_name = generateFileName(basename($_FILES["post_file"]["name"][0]));

                $target_file = __DIR__ . $dist . $file_name;
                if (move_uploaded_file($_FILES["post_file"]["tmp_name"][0], $target_file)) {
                    if ($db->query("INSERT INTO `posts`(`author`, `caption`, `media_type`, `media_url`) VALUES (?,?,?,?)", array($auth->output['id'], $_POST['post_caption'], $filetype, $dist . $file_name))) {
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
            echo 'File upload failed with error code ' . $_FILES['post_file']['error'][0];
        }
        die();
    }

    $profile_img = $url->home() .$auth->output['profile_img'];
    $first_name = strtok($auth->output['name'], " ");

    /** Body of the page. */
    $body = <<<EOPAGE
        <div class="middle1 box1">
            <div class="comment">
                <img class="commentimage" src="{$profile_img}" alt="error">
                <textarea id="post_caption" style="width: 100%; min-height: 50px; max-height: 50px;" name="post_txt"  class="commenttext" placeholder="What's in your mind {$first_name}"></textarea>
            </div>
            <div class="box111">
                <button id="save_post" class="commentbutton">post</button>
                <input id="post_file" type="file" name="post-file" name="post-file" accept="image/*,video/*">
            </div>
            <div id="show_post"></div>
            <div id="msg_post" style="text-align:center;"></div>
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
EOPAGE;
    /** Details configuration for the page. */
    $details = array(
        'title' => 'Home'
    );

    /** Required files for the page. */
    $files = array(
        'js' => [
            '/assets/js/jquery.min.js',
            '/assets/js/save_post.js',
            '/assets/js/load_post.js',
        ],
        'custom_js' => [
            <<<EOPAGE
        $(document).on('click', '.like-btn', function() {
            var likebtn = $(this);
            var postId = $(this).data('post-id');
            var likeCountElement = $("#like_count_"+postId);
            var likeCountText = likeCountElement.text();

            // Send AJAX request to server
            $.ajax({
                type: 'POST',
                url: 'index.php?like', // Point this to your PHP file handling likes
                data: { postId: postId },
                success: function (response) {
                    if (response === 'like') {
                        likebtn.css({
                            'color' : 'red'
                        });
                        likebtn.addClass('fa-solid');
                        likebtn.removeClass('fa-regular');
                        var newLikeCount = parseInt(likeCountText) + 1;
                        likeCountElement.text(newLikeCount);
                    } else if (response === 'unlike') {
                        likebtn.css({
                            'color' : 'black'
                        });
                        likebtn.removeClass('fa-solid');
                        likebtn.addClass('fa-regular');
                        var newLikeCount = parseInt(likeCountText) - 1;
                        likeCountElement.text(newLikeCount);
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
EOPAGE,
            <<<EOPAGE
        $(document).on('click', '.save-btn', function() {
            var savebtn = $(this);
            var postId = $(this).data('post-id');

            // Send AJAX request to server
            $.ajax({
                type: 'POST',
                url: 'index.php?save', // Point this to your PHP file handling likes
                data: { postId: postId },
                success: function (response) {
                    if (response === 'saved') {
                        savebtn.addClass('fa-solid');
                        savebtn.removeClass('fa-regular');
                    } else if (response === 'unsaved') {
                        savebtn.removeClass('fa-solid');
                        savebtn.addClass('fa-regular');
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
EOPAGE,

        ]
    );

    /** Creating the page. */
    new page($details, $files, $body, false,  $auth->output);
} else {
    if (isset($_GET['save_post']) && isset($_FILES['post_file']) && isset($_POST['post_caption']))
    {
        echo "Login in first to upload.";
    } else if (isset($_GET['load']) && isset($_POST["limit"]) && isset($_POST["start"])) {
        die();
    } else {
        /** Goto login page */
        goto_login();
    }
}