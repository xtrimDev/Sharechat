$('document').ready(function() {
    /* handling form validation */
    $('#forgot_pass').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
        },
        messages: {
            email: "Enter a valid email",
        },
        submitHandler: forgot
    });

    /* Handling login functionality */
    function forgot() {
        const data = $("#forgot_pass").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_msg").fadeOut();
                $("#check").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#check").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#check").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Processing');
                    setTimeout(' window.location.href = "?forgot=match"; ',2000);
                } else {
                    $("#error_msg").fadeIn(1000, function(){
                        $("#check").html('Continue');
                        $("#error_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#check").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }
});