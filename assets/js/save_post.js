$(document).ready(function () {
    $("#save_post").click(function () {
        // Get the files and text data
        var fileInput = document.getElementById("post_file");
        var textInput = document.getElementById("post_caption");

        var files = fileInput.files;
        var textData = textInput.value;

        // Check if files are selected
        if (files.length === 0) {
            swal({
                title: "File not found!",
                text: "Please Select a file!",
                icon: "warning",
            });
            return;
        }

        // Check file size
        var maxSize = 50 * 1024 * 1024; // 100 MB
        for (var i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                swal({
                    title: "Large File found!",
                    text: "Please upload file less than or equal to 50MB only!",
                    icon: "warning",
                });
                return;
            }
        }

        // Create FormData object
        var formData = new FormData();

        // Append files and text data to FormData
        for (var i = 0; i < files.length; i++) {
            formData.append("post_file[]", files[i]);
        }

        formData.append("post_caption", textData);

        // Send data using AJAX
        $.ajax({
            url: 'index.php?save_post',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $("#save_post").html('<i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>');
                $("#save_post").attr('disabled', 'disabled');
                $("#post_file").attr('disabled', 'disabled');
                $("#post_caption").attr('disabled', 'disabled');
            },
            success: function (response) {
                if(response === "success"){
                    $("#save_post").html('<i class="fa-solid fa-spinner fa-spin-pulse"></i> &nbsp; Posting');
                    swal({
                        title: "Success!",
                        text: "your post has been posted!",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                        $("#post_file").val('');
                        $("#post_caption").val('');
                        $("#save_post").html('POST');
                        $("#save_post").removeAttr('disabled');
                        $("#post_file").removeAttr('disabled');
                        $("#post_caption").removeAttr('disabled');
                    });
                } else {
                    swal({
                        title: "Failed!",
                        text: response,
                        icon: "error",
                    }).then((value) => {
                        $("#save_post").html('POST');
                        $("#save_post").removeAttr('disabled');
                        $("#post_file").removeAttr('disabled');
                        $("#post_caption").removeAttr('disabled');
                    });
                }
            }
        });
    });
});