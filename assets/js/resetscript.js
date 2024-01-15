$('document').ready(function() {
    /* handling form validation */
    $('#reset_pass').validate({
        rules: {
            password: {
                required: true
            },
            _password: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: "Please enter a password.",
            _password: {
                required: "Please enter confirm password",
                equalTo: "Password didn't matched"
            },
        },
        submitHandler: reset_pass
    });

    /* Handling login functionality */
    function reset_pass() {
        const data = $("#reset_pass").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_msg").fadeOut();
                $("#change_password").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#change_password").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#otp_verify").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Changing');
                    setTimeout(' window.location.href = "auth.php"; ',2000);
                } else {
                    $("#error_msg").fadeIn(1000, function(){
                        $("#change_password").html('Change');
                        $("#error_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#change_password").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }
});
