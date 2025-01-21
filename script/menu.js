

$(document).ready(function() {

    function userIsLoggedIn() {
        return $('body').data('loggedin') === 'true';
    }

    function showToast() {
        var toast = $('#toast');
        toast.addClass('show'); // Show the toast

        // Automatically hide the toast after 5 seconds
        setTimeout(function() {
            toast.removeClass('show');
        }, 5000);
    }

    function getUserEmail() {
        return $('body').data('email');
    }

    $(".addItemBtn").click(function(e) {
        e.preventDefault(); // Prevent default form submission

        if (!userIsLoggedIn()) {
            showToast();
            return;
        }

        // Check if the button has the 'disabled-button' class
        if ($(this).hasClass('disabled-button')) {
            return; // Do nothing if the item is unavailable
        }

        // Retrieve data attributes from the button
        var pid = $(this).data('pid');
        var pname = $(this).data('pname');
        var pprice = $(this).data('pprice');
        var pimage = $(this).data('pimage');
        var pcode = $(this).data('pcode');
        var pqty = 1; // Default quantity

        var email = getUserEmail();

        $.ajax({
            url: 'action.php',
            method: 'post',
            data: {
                pid: pid,
                pname: pname,
                pprice: pprice,
                pqty: pqty,
                pimage: pimage,
                pcode: pcode,
                email: email
            },
            success: function(response) {
                // Optionally, display a success message
                $("#message").html(response);
                window.scrollTo(0, 0);
                load_cart_item_number();
            },
            error: function() {
                alert('An error occurred while adding the item to the cart.');
            }
        });
    });

    // Close button functionality
    $('.toast-close').click(function() {
        $('#toast').removeClass('show');
    });

    // Okay button redirection
    $('.toast-ok').click(function() {
        window.location.href = 'login.php'; // Redirect to login.php
    });

    load_cart_item_number();

    function load_cart_item_number() {
        $.ajax({
            url: 'action.php',
            method: 'get',
            data: {
                cartItem: "cart_item"
            },
            success: function(response) {
                $("#cart-item").html(response);
            },
            error: function() {
                console.log('Failed to load cart item number.');
            }
        });
    }

});
