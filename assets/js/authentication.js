const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

$('document').ready(function() {
    /* handling form validation */
    $('#LoginForm').validate({
        rules: {
            _email: {
                required: true,
                email: true
            },
            _password: {
                required: true,
            },
        },
        messages: {
            _email:{
                required: "Please enter an email.",
                email: "Please enter a valid email."
            },
            _password: "Please enter a password.",
        },
        submitHandler: login
    });

    /* Handling login functionality */
    function login() {
        const data = $("#LoginForm").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_msg").fadeOut();
                $("#sign_in").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#sign_in").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#sign_in").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Signing In');
                    setTimeout(' window.location.href = "index.php"; ',2000);
                } else if (response == "102") {
                    $("#sign_in").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Signing In');
                    setTimeout(' window.location.href = "auth.php?verify"; ',2000);
                } else {
                    $("#error_msg").fadeIn(1000, function(){
                        $("#sign_in").html('Sign In');
                        $("#error_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#sign_in").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }


    $('#RegistrationForm').validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
            },
        },
        messages: {
            name: "Please enter your name",
            email:{
                required: "Please enter an email.",
                email: "Please enter a valid email."
            },
            password: "Please enter a password.",
        },
        submitHandler: registration
    });

    /* Handling login functionality */
    function registration() {
        const data = $("#RegistrationForm").serialize();
        $.ajax({
            type : 'POST',
            url  : 'authentication.php',
            data : data,
            beforeSend: function(){
                $("#error_reg_msg").fadeOut();
                $("#sign_up").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#sign_up").attr('disabled', 'disabled');
            },
            success : function(response){
                if(response === "success"){
                    $("#sign_up").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Signing up');
                    setTimeout(' window.location.href = "auth.php?verify"; ',2000);
                } else {
                    $("#error_reg_msg").fadeIn(1000, function() {
                        $("#sign_up").html('Sign up');
                        $("#error_reg_msg").html('<div class="msg_danger"> <i class="fa-solid fa-circle-info"></i> &nbsp; '+response+'</div>');
                        $("#sign_up").removeAttr('disabled');
                    });
                }
            }
        });
        return false;
    }
});