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

    if (isset($_GET['search_now']) && isset($_POST['inputValue']))
    {
        if (!empty($_POST['inputValue'])) {
            $query = $db->query("SELECT * FROM `user` WHERE (`unique_name` LIKE '%" . trim($_POST['inputValue']) ."%' AND `id` <> ?) OR (`name` LIKE '%" . trim($_POST['inputValue']) ."%' AND `id` <> ?);", array($auth->output['id'], $auth->output['id']));

            if ($query->numRows()) {
                $data = $query->fetchAll();

                foreach ($data as $rand => $details) {
                    echo <<<EOPAGE
            <div class="reqbox" style=" margin-top: 20px;">
                <div class="reqimg2">
                    <img alt="user" src="{$details['profile_img']}">
                </div>
                <div class="reqbox2">
                    <div class="msg222">
                        <a class="msgername" href="#">{$details['name']}</a>
                        <a class="msgby" href="javascript:void(0)" class="menuname">@{$details['unique_name']}</a>
                    </div>
                    <div class="reqbtns">
                        <button class="accept add_friend" data-user-id="{$details['id']}">Add friend</button>
                    </div>
                </div>
            </div>
EOPAGE;
                }
            }
        }

        die();
    }

    if (isset($_GET['add_friend']) && isset($_POST['userId']))
    {
        if ($db->query("INSERT INTO `frind_req`(`from`, `to`, `status`) VALUES (?,?,'0')", array($auth->output['id'], $_POST['userId'])))
        {
            echo "success";
        }
        die();
    }

    if (isset($_GET['reject']) && isset($_POST['userId']))
    {
        if ($db->query("DELETE FROM `frind_req` WHERE `from` = ? AND `to` = ?", array($_POST['userId']), $auth->output['id']))
        {
            echo "success";
        }

        die();
    }

    if (isset($_GET['accept']) && isset($_POST['userId']))
    {
        if ($db->query("UPDATE `frind_req` SET `status`='1' WHERE `from` = ? AND `to` = ?", array($_POST['userId']), $auth->output['id']))
        {
            echo "success";
        }

        die();
    }

    if (isset($_GET['load_post'])) 
    {
        $run = $db->query("SELECT * FROM `frind_req` WHERE `to` = ? AND `status` = '0'", $auth->output['id']);
        if ($run->numRows())
        {
            $data = $run->fetchAll();

            foreach ($data as $rand => $details)
            {
                $from_id = $details['from'];
                $auth->getuserdata($from_id);

                echo <<<EOPAGE
            <div class="reqbox">
                <div class="reqimg2">
                    <img alt="user" src="{$auth->output['profile_img']}">
                </div>
                <div class="reqbox2">
                    <div class="msg222">
                        <a class="msgername" href="#">{$auth->output['name']}</a>
                        <a class="msgby" href="javascript:void(0)" class="menuname">@{$auth->output['unique_name']}</a>
                    </div>
                    <div class="reqbtns">
                        <button class="accept" data-user-id="{$auth->output['id']}">Accept</button>
                        <button class="reject" data-user-id="{$auth->output['id']}">Reject</button>
                    </div>
                </div>
            </div>
EOPAGE;

            }
        }
        die();
    }
    $details = array(
        'title' => 'Friends'
    );

    $files['css'][] = '/assets/css/friends.css';
    $files['js'][] = '/assets/js/jquery.min.js';
    $files['custom_js'][] = <<<EOPAGE
    $(document).on('click', '.add_friend', function() {
        var userId = $(this).data('user-id');
        
        $.ajax({
                type: 'POST',
                url: 'friends.php?add_friend', // Point this to your PHP file handling likes
                data: {userId: userId},
                success: function (response) {
                    if (response === 'success') {
                        $(this).remove;
                    }  else {
                        swal({
                            title: "Failed!",
                            text: 'something went wrong!',
                            icon: "danger",
                        });
                    }
                }
            });
    });
    
    $(document).on('click', '.reject', function() {
        var userId = $(this).data('user-id');
        
        $.ajax({
                type: 'POST',
                url: 'friends.php?reject', // Point this to your PHP file handling likes
                data: {userId: userId},
                success: function (response) {
                    if (response === 'success') {
                        location.reload();
                    }  else {
                        swal({
                            title: "Failed!",
                            text: 'something went wrong!',
                            icon: "danger",
                        });
                    }
                }
            });
    });
    
    $(document).on('click', '.accept', function() {
        var userId = $(this).data('user-id');
        
        $.ajax({
                type: 'POST',
                url: 'friends.php?accept', // Point this to your PHP file handling likes
                data: {userId: userId},
                success: function (response) {
                    if (response === 'success') {
                        location.reload();
                    }  else {
                        swal({
                            title: "Failed!",
                            text: 'something went wrong!',
                            icon: "danger",
                        });
                    }
                }
            });
    });
    
    $(document).on('click', '#search_now', function() {
        $(".reqbox").remove();
        var inputValue = document.getElementById("search_friend_new").value;
        
        $.ajax({
            type : 'POST',
            data : {inputValue: inputValue},
            url  : 'friends.php?search_now',
            success : function(response){
                if (response === '') 
                {
                    swal({
                        title: "Nothing Found!",
                        text: 'Try another keyword!',
                    });
                } else {
                    $('.pumk').append(response);
                }
            }
        });
    });
EOPAGE;

    $files['custom_js'][] = <<<EOPAGE
    
    $(document).ready(function(){
        $.ajax({
            type : 'GET',
            data : {data: 1},
            url  : 'friends.php?load_post',
            success : function(response){
                if (response === '') 
                {
                    $('.middle1').html('<div class="pumk"> <h1 style="color: white">Search People you want to add</h1> <div class="search"> <input type="text" id="search_friend_new" placeholder="Enter the Person you want to search"><button id="search_now">Search</button></div></div>');
                } else {
                    $('#friend_list').html(response);
                }
            }
        });
    });
EOPAGE;

    $body = <<<EOPAGE
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="middle1 ">
        <div class="frie-h">
            <h2>Friend requests</h2>
        </div>
        <div class="friends-container" id="friend_list">
        </div>
        <div class="pumk">
            <h1 style="color: white">Search People you want to add</h1>
            <div class="search">
                <input type="text" id="search_friend_new" placeholder="Enter the Person you want to search">
                <button id="search_now">Search</button>
            </div>
        </div>
    </div>

EOPAGE;
    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

    new page($details, $files, $body, false,  $auth->output);
} else {
    /** Goto login page */
    goto_login();
}