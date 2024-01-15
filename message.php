<?php
/** Loading all required files. */
require_once __DIR__ . '/includes/load.php';

/** Checking the required filters. */
$filter = new filter();
$db = new Database();
$auth = new Authentication();
$url = new url();

if ($filter->check_authentication())
{
    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);
    $loggedInId = $auth->output['id'];

    if (isset($_GET['find_chat']) && !empty($_POST['name']))
    {
        $run = $db->query("SELECT * FROM `user` WHERE (`unique_name` LIKE '%" . trim($_POST['name']) ."%' AND `id` <> ?) OR (`name` LIKE '%" . trim($_POST['name']) ."%' AND `id` <> ?)", array($auth->output['id'], $auth->output['id']));

        if ($run->numRows())
        {
            foreach ($run->fetchAll() as $rand => $data)
            {
                echo <<<EOPAGE
                    <div class="block user_chat" data-user-id="{$data['id']}">
                        <div class="imgBox">
                            <img src="{$url->home()}{$data['profile_img']}" class="cover" alt="user">
                        </div>
                        <div class="details">
                            <div class="listHead">
                                <h4>{$data['name']}</h4>
                            </div>
                            <div class="message_p">
                                <p>@{$data['unique_name']}</p>
                            </div>
                        </div>
                    </div>
EOPAGE;
            }
        } else {
            echo <<<EOPAGE
                    <style>
                        #chatlist {
                            display: flex;
                            flex-direction: column;
                            flex-wrap: nowrap;
                            align-content: center;
                            justify-content: center;
                        }
                    </style>
                    <div id="no_data">
                        <span>No Chat found</span>
                    </div>
EOPAGE;
        }
        die();
    }

    if (isset($_GET['send_msg']) && !empty($_POST['inputData']) && !empty($_POST['userId']))
    {
        $run = $db->query("INSERT INTO `chats`(`from`, `to`, `msg`) VALUES (?, ?, ?)", array($loggedInId, trim($_POST['userId']), $_POST['inputData']));
        die();
    }

    if (isset($_POST['userId']) && isset($_REQUEST['second'])) {
        /** show chats of user */
        $auth->getuserdata(trim($_POST['userId']));

        /** status of msg will be read */
        $db->query("UPDATE `chats` SET `read` = ? WHERE `from` = ? AND `to` = ? AND `read` = ?", array('1', $auth->output['id'], $loggedInId, '0'));

        $run = $db->query("SELECT * FROM `chats` WHERE (`from` = ? AND `to` = ?) OR (`from` = ? AND `to` = ?) ORDER BY `at` ASC", array($loggedInId,trim($_POST['userId']),trim($_POST['userId']),$loggedInId));

        if ($run->numRows()) {
            foreach ($run->fetchAll() as $rand => $data) {
                if ($data['from'] == $loggedInId) {
                    $class = 'my_msg';
                } else {
                    $class = 'friend_msg';
                }
                echo <<<EOPAGE
                                
                                <div class="message {$class}">
                                    <p>{$data['msg']}<br><span>{$data['at']}</span></p>
                                </div>
EOPAGE;

            }
        }
        die();
    }

    if (isset($_POST['userId']) && isset($_REQUEST['first'])) {
        /** show chats of user */
            $auth->getuserdata(trim($_POST['userId']));
            echo <<<EOPAGE
                            <div class="header">
                                <div class="imgText">
                                    <div class="userimg">
                                        <img onclick="window.location.href='{$url->home()}/profile.php?user={$auth->output['unique_name']}'" src="{$url->home()}{$auth->output['profile_img']}" alt="img-user" class="cover">
                                    </div>
                                    <h4 style="cursor: pointer" onclick="window.location.href='{$url->home()}/profile.php?user={$auth->output['unique_name']}'">{$auth->output['name']} <br><!--<span>online</span>--></h4>
                                </div>
                                <ul class="nav_icons">
                                    <li><ion-icon name="search-outline"></ion-icon></li>
                                    <li><ion-icon name="ellipsis-vertical"></ion-icon></li>
                                </ul>
                            </div>
                            <div class="chatbox">
EOPAGE;
            /** status of msg will be read */
            $db->query("UPDATE `chats` SET `read` = ? WHERE `from` = ? AND `to` = ? AND `read` = ?", array('1', $auth->output['id'], $loggedInId, '0'));

            $run = $db->query("SELECT * FROM `chats` WHERE (`from` = ? AND `to` = ?) OR (`from` = ? AND `to` = ?) ORDER BY `at` ASC", array($loggedInId,trim($_POST['userId']),trim($_POST['userId']),$loggedInId));

            if ($run->numRows()) {
                foreach ($run->fetchAll() as $rand => $data) {
                    if ($data['from'] == $loggedInId) {
                        $class = 'my_msg';
                    } else {
                        $class = 'friend_msg';
                    }
                    echo <<<EOPAGE
                                
                                <div class="message {$class}">
                                    <p>{$data['msg']}<br><span>{$data['at']}</span></p>
                                </div>
EOPAGE;

                }
            }
            echo <<<EOPAGE
                            </div>
                            <div class="chat_input">
                                <i class="fa-regular fa-face-smile" id="add_emoji"></i>
                                <input type="text" autocomplete="off" id="text_msg" autofocus="autofocus" data-user-id="{$auth->output['id']}" placeholder="Type a message">
                            </div>
EOPAGE;

        die();
    }

    if (isset($_REQUEST['show_friend']))
    {
        $run = $db->query("SELECT * FROM `chats` WHERE (`from` = ? AND `to` <> ?) OR (`from` <> ? AND `to` = ?)", array($loggedInId,$loggedInId,$loggedInId,$loggedInId));

        if ($run->numRows())
        {
            $data = $run->fetchAll();
            $id = [];

            foreach ($data as $rand => $details)
            {
                if ($details['to'] == $auth->output['id'])
                {
                    $id[] = $details['from'];
                } else {
                    $id[] = $details['to'];
                }
            }

            $id = array_unique($id);
            $data_arr = [];
            $count = 0;
            foreach ($id as $chat_friend)
            {
                $auth->getuserdata($chat_friend);

                /** Get the last msg for $chat_friend */
                $query = $db->query("SELECT * FROM `chats` WHERE (`from` = ? AND `to` = ?) OR (`from` = ? AND `to` = ?) ORDER BY `at` ASC LIMIT 0,1", array($loggedInId,$auth->output['id'],$auth->output['id'],$loggedInId));
                if ($query->numRows())
                {
                    $data = $query->fetchArray();

                    $data_arr[$count]['id'] = $auth->output['id'];
                    $data_arr[$count]['name'] = $auth->output['name'];
                    $data_arr[$count]['profile_img'] = $auth->output['profile_img'];
                    $data_arr[$count]['last_msg']['msg'] = $data['msg'];
                    $data_arr[$count]['last_msg']['at'] = $data['at'];

                    $qry = $db->query("SELECT * FROM `chats` WHERE `from` = ? AND `to` = ? AND `read` = ?", array($chat_friend, $loggedInId, '0'));

                    $data_arr[$count]['last_msg']['unread'] = $qry->numRows();
                }
                $count++;
            }

            /** Short the array with the 'at' index. */
            usort($data_arr, function($a, $b) {
                return strtotime($b['last_msg']['at']) - strtotime($a['last_msg']['at']);
            });

            foreach ($data_arr as $rand => $data)
            {
                if ($data['last_msg']['unread']) {
                    $read = ' unread';
                    $unread_msg = <<<EOPAGE

                        <b>{$data['last_msg']['unread']}</b>
EOPAGE;
                } else {
                    $read = '';
                    $unread_msg = '';
                }

                echo <<<EOPAGE
                    <div class="block user_chat{$read}" data-user-id="{$data['id']}">
                        <div class="imgBox">
                            <img src="{$url->home()}{$data['profile_img']}" class="cover" alt="user">
                        </div>
                        <div class="details">
                            <div class="listHead">
                                <h4>{$data['name']}</h4>
                                <p class="time">{$data['last_msg']['at']}</p>
                            </div>
                            <div class="message_p">
                                <p>{$data['last_msg']['msg']}</p> {$unread_msg}
                            </div>
                        </div>
                    </div>
EOPAGE;
            }
        } else {
            echo <<<EOPAGE
                    <style>
                        #chatlist {
                            display: flex;
                            flex-direction: column;
                            flex-wrap: nowrap;
                            align-content: center;
                            justify-content: center;
                        }
                    </style>
                    <div id="no_data">
                        <span>No Chat found</span>
                    </div>
EOPAGE;
        }
        die();
    }

    /** Details configuration for the page. */
    $details = array(
        'title' => 'Message'
    );

    /** Required files for the page. */
    $files = array(
        'css' => ['/assets/css/chatstyle.css'],
        'js' => '/assets/js/jquery.min.js"></script><script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>',
        'custom_js' => [
            <<<EOPAGE
    $(document).ready(function(){
        show_friend();
                
        function normalizeAndCompare(html1, html2) {
            function removeTrailingSpaces(html) {
                return html.replace(/\s+$/, '');
            }

            const normalizedHtml1 = removeTrailingSpaces(html1).replace(/\s+/g, ' ').trim();
            const normalizedHtml2 = removeTrailingSpaces(html2).replace(/\s+/g, ' ').trim();

            if (normalizedHtml1 === normalizedHtml2) {
                return true;
            } else {
                return  false;
            }
        }
       
        function show_friend() {
            $.ajax({
                type: 'GET',
                url: 'message.php?show_friend',
                success: function (friends_list) {
                    chatlist = $("#chatlist").html();
                    if (!normalizeAndCompare(chatlist, friends_list)) {
                        $("#chatlist").html(friends_list);
                    }
                }
            });
        }
        
        var a = 0;
        $(document).on('keyup', '#find_chat', function(event) {
            var find_chat = $('#find_chat').val();
            
            if (find_chat != '') {
                a = 1;
                // Send data to the server using AJAX
                $.ajax({
                    type: 'POST',
                    url: 'message.php?find_chat',
                    data: { 
                        name: find_chat,
                      },
                    success: function(response) {
                        $("#chatlist").html(response);
                    }
                });
            } else {
                a = 0;
                show_friend();
            }
        });
        
        setInterval(function() {
            if (a == 0)
            {
                show_friend();
            }
        }, 1000);
        
        function show_chat(userId) {
            $.ajax({
                type: 'POST',
                data: {userId : userId, first: 1},
                url: 'message.php?show_chat',
                beforeSend: function(){
                    $(".no_selection").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                },
                success: function (show_chat) {
                    rightSide = $(".rightSide").html();
                    
                    if (!normalizeAndCompare(rightSide, show_chat)) {
                        $(".rightSide").html(show_chat);

                        $(".chatbox").animate({
                            scrollTop: $('.chatbox')[0].scrollHeight - $('.chatbox')[0].clientHeight
                          }, 1);
                    }
                }
            });
        }
        
        function refresh_chat(userId) {
            $.ajax({
                type: 'POST',
                data: {userId : userId, second: 1},
                url: 'message.php?refresh_chat',
                success: function (show_chat) {
                    chatbox = $(".chatbox").html();
                    
                    if (!normalizeAndCompare(chatbox, show_chat)) {
                        $(".chatbox").html(show_chat);
                        //var audio = $("#incomming")[0];
                            //audio.play();

                        $(".chatbox").animate({
                            scrollTop: $('.chatbox')[0].scrollHeight - $('.chatbox')[0].clientHeight
                          }, 1);
                    }
                }
            });
        }

        var userId = '';
        $(document).on('click', '.user_chat', function() {
            userId = '';
            userId = $(this).data("user-id");
            show_chat(userId);
            setInterval(function() {
                refresh_chat(userId);
            }, 500);
        });
        
        
        $(document).on('keypress', '#text_msg', function(event) {
            // Check if the Enter key is pressed (key code 13)
            if (event.keyCode === 13) {
                // Prevent the default form submission
                event.preventDefault();
                
                $("#text_msg").attr('disabled', 'disabled');
                  
                // Get the entered data
                var inputData = $('#text_msg').val();
                var userId = $('#text_msg').data('user-id');
                
                if (inputData != '') {
                    // Send data to the server using AJAX
                    $.ajax({
                        type: 'POST',
                        url: 'message.php?send_msg',
                        data: { 
                            inputData: inputData,
                            userId: userId
                          },
                        success: function(response) {
                            $('#text_msg').val('');
                            $('#text_msg').focus();
                            var audio = $("#sent_msg")[0];
                            audio.play();
                            $("#text_msg").removeAttr('disabled');
                            $('#text_msg').focus();
                        },
                        error: function() { 
                            $("#text_msg").removeAttr('disabled');
                            $('#text_msg').focus();
                        }
                    });
                } else {
                    $("#text_msg").removeAttr('disabled');
                    $('#text_msg').focus();
                }
            }
        });
    });
        const bun = document.querySelector('#add_emoji');
        const picker = new EmojiButton();
        $(document).on('click', '#add_emoji', function() {
                //const picker = new EmojiButton();s
                picker.togglePicker(bun);
        });

        picker.on('emoji', emoji => {
            document.querySelector('#text_msg').value += emoji;
        });

        
EOPAGE
            ]
    );

    /** Body of the page. */
    $body = <<<EOPAGE
        <div class="container" style="z-index: -10; margin-top: 18px; overflow: hidden; border-radius: 10px;">
            <div class="leftSide" style="border-top-left-radius: 10px;">
                <div class="header" style="border-top-left-radius: 10px">
                    <h2>Chats</h2>
                </div>
                <div class="search_chat">
                    <div style="background: white; display: flex; flex-direction: row; flex-wrap: nowrap; align-content: center; justify-content: space-around; align-items: center;">
                        <input autocomplete="off" type="text" id="find_chat" style="height: 35px" placeholder="Search or start new chat..">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>                
                </div>
                <!-- CHAT LIST -->
                <div class="chatlist" id="chatlist">
                </div>
            </div>
            <div class="rightSide" style="display: flex; flex-direction: column;">
                <div class="no_selection">
                    <i class="fa-sharp fa-solid fa-circle-info fa-2xl" style="color: #1a3b75;"></i>
                    <span>Select a friend to start chat.</span>
                </div>
            </div>
            <audio style="display: none;" id="sent_msg" src="{$url->home()}/assets/audio/sent_message.mp3"></audio>
            <audio style="display: none;" id="incomming" src="{$url->home()}/assets/audio/incoming.mp3"></audio>
        </div>
EOPAGE;
    $auth->getuserdata(NULL, '', $_COOKIE[SITE_NAME . '_AUTH_FILTER_VALUE']);

    /** Creating the page. */
    new page($details, $files, $body, false,  $auth->output);
} else{
    /** Goto login page */
    goto_login();
}