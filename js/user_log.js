$(document).ready(function(){
  // Get Report passenger
  $(document).on('click', '#user_log', function(){
    
    var date_sel = $(".date_sel:checked").val();
    var date_sel_start = $('#date_sel_start').val();
    var date_sel_end = $('#date_sel_end').val();
    var time_sel = $(".time_sel:checked").val();
    var time_sel_end = $('#time_sel_end').val();
    var time_sel_start = $('#time_sel_start').val();
    var card_sel = $('#card_sel option:selected').val();
    var dev_uid = $('#dev_sel option:selected').val();
    var class_sel = $('#class option:selected').val();
    var no_room = $('#no_room option:selected').val();
    var status_sel = $('#status_sel option:selected').val(); // Medan baru untuk status (IN/OUT)
    var phone_number = $('#phone_number option:selected').val();

    $.ajax({
      url: 'user_log_up.php',
      type: 'POST',
      data: {
        'date_sel': date_sel,
        'date_sel_start': date_sel_start,
        'date_sel_end': date_sel_end,
        'time_sel': time_sel,
        'time_sel_end': time_sel_end,
        'time_sel_start': time_sel_start,
        'card_sel': card_sel,
        'device_dep': dev_uid,
        'class': class_sel,
        'no_room': no_room,
        'status_sel': status_sel, // Hantar status sebagai parameter
        'phone_number': phone_number,
      },
      success: function(response){
        $('.up_info2').fadeIn(500);
        $('.up_info2').text("The Filter has been selected!");

        $('#Filter-export').modal('hide');
        setTimeout(function () {
            $('.up_info2').fadeOut(500);
        }, 5000);

        // Update the content of #userslog with the new filtered data
        $.ajax({
          url: "user_log_up.php",
          type: 'POST',
          data: {
            'date_sel': date_sel,
            'date_sel_start': date_sel_start,
            'date_sel_end': date_sel_end,
            'time_sel': time_sel,
            'time_sel_end': time_sel_end,
            'time_sel_start': time_sel_start,
            'device_dep': dev_uid,
            'class': class_sel,
            'no_room': no_room,
            'card_sel': card_sel,
            'status_sel': status_sel, // Hantar status sebagai parameter
            'phone_number': phone_number,
            'select_date': 0,
          }
        }).done(function(data) {
          $('#userslog').html(data);
        });
      }
    });
  });
});
