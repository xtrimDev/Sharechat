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
        $query = $db->query("SELECT * FROM `user_save` WHERE `user_id` = ?", array($auth->output['id']));

        if ($query->numRows())
        {
            $data = $query->fetchAll();
            $id = '';
            foreach($data as $rand => $details){
                if (count($data) - 1 > $rand)
                    $id .= $details['post_id'] . ",";
                else
                    $id .= $details['post_id'];
            }

            $run = $db->query("SELECT * FROM `posts` WHERE `id` IN ({$id}) ORDER BY `posts`.`date_of_publish` DESC LIMIT ?, ?", array(trim($_POST['start']), trim($_POST['limit'])));

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
        }
        die();
    }

    /** Body of the page. */
    $body = <<<EOPAGE
        <div class="middle1 box1">
            <div id="show_post"></div>
            <div id="msg_post" style="text-align:center;"></div>
        </div>
EOPAGE;
    /** Details configuration for the page. */
    $details = array(
        'title' => 'Saved'
    );

    /** Required files for the page. */
    $files = array(
        'js' => [
            '/assets/js/jquery.min.js',
            '/assets/js/save_post.js',
            '/assets/js/load_saved.js',
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
        /** Goto login page */
        goto_login();
}