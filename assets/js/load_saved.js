$(document).ready(function(){

    var limit = 3;
    var start = 0;
    var action = 'inactive';
    function load_posts(limit, start)
    {
        $.ajax({
            url:"saved.php?load",
            method:"POST",
            data:{limit:limit, start:start},
            cache:false,
            success:function(data)
            {
                if(data == '')
                {
                    $('#msg_post').html('<span style="background: transparent; color: white; font-size: 20px; margin-bottom: 5px;">No Data Found</span>');
                    action = 'active';
                } else {
                    if ($('#show_post').html() === '') {
                        $('#show_post').html("<h1 style='color: white; margin-bottom: 20px'>Saved Posts</h1>");
                    }
                    $('#show_post').append(data);
                    //$('#msg_post').html('<i class="fa-solid fa-spinner fa-spin-pulse load_more"></i>');
                    action = "inactive";
                }
            }
        });
    }

    if(action == 'inactive')
    {
        action = 'active';
        load_posts(limit, start);
    }
    $(window).scroll(function(){
        if($(window).scrollTop() + $(window).height() > $("#show_post").height() && action == 'inactive')
        {
            $('#msg_post').html('<i style="color: white" class="fa-solid fa-spinner fa-spin-pulse load_more" ></i>');
            action = 'active';
            start = start + limit;
            setTimeout(function(){
                load_posts(limit, start);
            }, 1000);
        }
    });

});