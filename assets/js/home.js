
    // searchbtn
    //search_friend_home
    function redirectToPage() {
        // Create a form element
        var search_key = $("#search_friend_home").val();
        var form = document.createElement('form');

        if (search_key !== '') {
            // Set the form attributes
            form.method = 'GET';
            form.action = 'index.php'; // Set the destination page URL

            // Create an input element for each parameter and append it to the form
            var input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'search_key';
            input1.value = search_key;
            form.appendChild(input1);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }

    }

    $(document).on('click', '.searchbtn', function() {

        redirectToPage();
    });

    $(document).on('keypress', '#search_friend_home', function(event) {
        // Check if the Enter key is pressed (key code 13)
        if (event.keyCode === 13) {
            // Prevent the default form submission
            redirectToPage();
        }
    });