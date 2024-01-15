const codes = document.querySelectorAll('.code')

codes[0].focus()

codes.forEach((code, idx) => {
    code.addEventListener('keydown', (e) => {
        if(e.key >= 0 && e.key <=9) {
            codes[idx].value = ''
            setTimeout(() => codes[idx + 1].focus(), 10)
        } else if(e.key === 'Backspace') {
            setTimeout(() => codes[idx - 1].focus(), 10)
        }
    })
})

$('document').ready(function() {
    /* handling form validation */
    $('#verify_otp').validate({
        rules: {
            int1: {
                required: true
            },
            int2: {
                required: true
            },
            int3: {
                required: true
            },
            int4: {
                required: true
            },
            int5: {
                required: true
            },
            int6: {
                required: true
            },
        },
        messages: {
            int1: "Please fill it.",
            int2: "Please fill it.",
            int3: "Please fill it.",
            int4: "Please fill it.",
            int5: "Please fill it.",
            int6: "Please fill it.",
        },
        submitHandler: otp_verify
    });

    /* Handling login functionality */
    function otp_verify() {
        const data = $("#verify_otp").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_msg").fadeOut();
                $("#otp_verify").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#otp_verify").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#otp_verify").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Verifying');
                    setTimeout(' window.location.href = "index.php"; ',2000);
                } else {
                    $("#error_msg").fadeIn(1000, function(){
                        $("#otp_verify").html('Verify');
                        $("#error_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#otp_verify").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }


    /** forgot**/
    /* handling form validation */
    $('#verify_otp_forgot').validate({
        rules: {
            int1: {
                required: true
            },
            int2: {
                required: true
            },
            int3: {
                required: true
            },
            int4: {
                required: true
            },
            int5: {
                required: true
            },
            int6: {
                required: true
            },
        },
        messages: {
            int1: "Please fill it.",
            int2: "Please fill it.",
            int3: "Please fill it.",
            int4: "Please fill it.",
            int5: "Please fill it.",
            int6: "Please fill it.",
        },
        submitHandler: otp_verify_forgot
    });

    /* Handling login functionality */
    function otp_verify_forgot() {
        const data = $("#verify_otp_forgot").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_msg").fadeOut();
                $("#otp_verify_forgot").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#otp_verify_forgot").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#otp_verify").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Verifying');
                    setTimeout(' window.location.href = "auth.php?reset"; ',2000);
                } else {
                    $("#error_msg").fadeIn(1000, function(){
                        $("#otp_verify_forgot").html('Verify');
                        $("#error_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#otp_verify_forgot").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }
});
