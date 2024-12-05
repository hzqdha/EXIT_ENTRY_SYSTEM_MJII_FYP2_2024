$(document).ready(function() {
    function updateTable() {
        $.ajax({
            url: "manage_users_up.php",
            type: "GET",
            success: function(data) {
                $('tbody.table-secondary').html(data); // Update the table body with new data
            },
            error: function(err) {
                console.error("Error updating table: ", err);
            }
        });
    }

    // Add user
    $(document).on('click', '.user_add', function() {
        var user_id = $('#user_id').val();
        var name = $('#name').val();
        var gender = $(".gender:checked").val();
        var device_dep = $('#dept').val();
        var Class = $('#class').val();
        var no_room = $('#rooms').val();
        var phone_number = $('#phone').val();

        $.ajax({
            url: 'manage_users_conf.php',
            type: 'POST',
            data: {
                'Add': 1,
                'user_id': user_id,
                'name': name,
                'gender': gender,
                'device_dep': device_dep,
                'class': Class,
                'no_room': no_room,
                'phone_number': phone_number,
            },
            success: function(response) {
                if (response == 1) {
                    $('#user_id').val('');
                    $('#name').val('');
                    $('input[name="gender"]').prop('checked', false);
                    $('#device_dep').val('');
                    $('#class').val('');
                    $('#no_room').val('');
                    $('#phone_number').val('');

                    $('.alert_user').fadeIn(500).html('<p class="alert alert-success">A new User has been successfully added</p>');
                } else {
                    $('.alert_user').fadeIn(500).html('<p class="alert alert-danger">' + response + '</p>');
                }

                setTimeout(function() {
                    $('.alert').fadeOut(500);
                }, 5000);

                updateTable();
            },
            error: function(err) {
                console.error("Error adding user: ", err);
            }
        });
    });

    // Update user
    $(document).on('click', '.user_upd', function() {
        var user_id = $('#user_id').val();
        var name = $('#name').val();
        var gender = $(".gender:checked").val();
        var device_dep = $('#dept').val();
        var Class = $('#class').val();
        var no_room = $('#rooms').val();
        var phone_number = $('#phone').val();

        $.ajax({
            url: 'manage_users_conf.php',
            type: 'POST',
            data: {
                'Update': 1,
                'user_id': user_id,
                'name': name,
                'gender': gender,
                'device_dep': device_dep,
                'class': Class,
                'no_room': no_room,
                'phone_number': phone_number,
            },
            success: function(response) {
                if (response == 1) {
                    $('#user_id').val('');
                    $('#name').val('');
                    $('input[name="gender"]').prop('checked', false);
                    $('#device_dep').val('');
                    $('#class').val('');
                    $('#no_room').val('');
                    $('#phone_number').val('');

                    $('.alert_user').fadeIn(500).html('<p class="alert alert-success">The selected User has been updated!</p>');
                } else {
                    $('.alert_user').fadeIn(500).html('<p class="alert alert-danger">' + response + '</p>');
                }

                setTimeout(function() {
                    $('.alert').fadeOut(500);
                }, 5000);

                updateTable();
            },
            error: function(err) {
                console.error("Error updating user: ", err);
            }
        });
    });

    // Delete user
$(document).on('click', '.user_rmo', function() {
    var user_id = $('#user_id').val();

    bootbox.confirm({
        message: "<span style='color: black;'>Do you really want to delete this Student?</span>",
        callback: function(result) {
            if (result) {
                $.ajax({
                    url: 'manage_users_conf.php',
                    type: 'POST',
                    data: {
                        'delete': 1,
                        'user_id': user_id,
                    },
                    success: function(response) {
                        if (response == 1) {
                            $('#user_id').val('');
                            $('#name').val('');
                            $('input[name="gender"]').prop('checked', false);
                            $('#device_dep').val('');
                            $('#class').val('');
                            $('#no_room').val('');
                            $('#phone_number').val('');

                            $('.alert_user').fadeIn(500).html('<p class="alert alert-success">The selected User has been deleted!</p>');
                        } else {
                            $('.alert_user').fadeIn(500).html('<p class="alert alert-danger">' + response + '</p>');
                        }

                        setTimeout(function() {
                            $('.alert').fadeOut(500);
                        }, 5000);

                        updateTable();
                    },
                    error: function(err) {
                        console.error("Error deleting user: ", err);
                    }
                });
            }
        }
    });
});


    // Select user
    $(document).on('click', '.select_btn', function() {
        var el = this;
        var card_uid = $(this).attr("id");

        $.ajax({
            url: 'manage_users_conf.php',
            type: 'GET',
            data: {
                'select': 1,
                'card_uid': card_uid,
            },
            success: function(response) {
                $(el).closest('tr').css('background', '#70c276');

                $('.alert_user').fadeIn(500).html('<p class="alert alert-success">The card has been selected!</p>');

                setTimeout(function() {
                    $('.alert').fadeOut(500);
                }, 5000);

                updateTable();

                // Ensure response is parsed as an array of objects
                if (Array.isArray(response) && response.length > 0) {
                    var user = response[0]; // Assuming the first item is the selected user data
                    console.log(response[0]);

                    var user_id = user.id;
                    var user_name = user.username;
                    var user_gender = user.gender;
                    var user_dev = user.device_dep;
                    var user_class = user.class;
                    var user_no_room = user.no_room;
                    var user_phone_number = user.phone_number;

                    $('#user_id').val(user_id);
                    $('#name').val(user_name);
                    $('input[name="gender"][value="' + user_gender + '"]').prop('checked', true);
                    $('#dept').val(user_dev);
                    $('#class').val(user_class);
                    $('#rooms').val(user_no_room);
                    $('#phone').val(user_phone_number);
                }
            },
            error: function(err) {
                console.error("Error selecting user: ", err);
            }
        });
    });
});