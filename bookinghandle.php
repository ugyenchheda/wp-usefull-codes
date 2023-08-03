//function to save reservation data into postmeta
function handle_event_booking() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_send_json_error('Invalid request method.');
    }

    if (isset($_POST['formData'])) {
        parse_str($_POST['formData'], $booking_details);

        $name = isset($booking_details['booking_details']['name']) ? sanitize_text_field($booking_details['booking_details']['name']) : '';
        $email = isset($booking_details['booking_details']['email']) ? sanitize_email($booking_details['booking_details']['email']) : '';
        $phone = isset($booking_details['booking_details']['phone']) ? sanitize_text_field($booking_details['booking_details']['phone']) : '';
        $nopep = isset($booking_details['booking_details']['nopep']) ? absint($booking_details['booking_details']['nopep']) : '';
        $booking_date = isset($booking_details['booking_details']['booking_date']) ? sanitize_text_field($booking_details['booking_details']['booking_date']) : '';
        $booking_message = isset($booking_details['booking_details']['booking_message']) ? sanitize_text_field($booking_details['booking_details']['booking_message']) : '';
		
        $post_id = isset($booking_details['event_id']) ? absint($booking_details['event_id']) : 0;

		
        $booking_info = "Name: $name\nEmail: $email\nPhone: $phone\nNo. of People: $nopep\nBooking Date: $booking_date";
        $existing_booking_info = get_post_meta($post_id, 'booking_details', true);
		
		$updated_booking_info = '';
        $existing_booking_info = get_post_meta($post_id, 'booking_details', true);
        if ($existing_booking_info) {
            $updated_booking_info .= wp_kses_post($existing_booking_info) . "\n\n";
        }
		
        $updated_booking_info .= " || Name: " . esc_html($name) . "  | ";
        $updated_booking_info .= " | Email: " . esc_html($email) . "  | ";
        $updated_booking_info .= " | Phone: " . esc_html($phone) . "  | ";
        $updated_booking_info .= " | No. of People: " . esc_html($nopep) . " |  ";
        $updated_booking_info .= " | Booking Date: " . esc_html($booking_date) . "  || ";
        $updated_booking_info .= " | Booking message: " . esc_html($booking_message) . "  || ";

        // Save the booking details as post meta
        update_post_meta($post_id, 'booking_details', $updated_booking_info);

        // Send the booking details via email
		$email = 'ugyenchheda@gmail.com';
        $subject = 'Booking Reservation Details';
        $message = $booking_info; 
        $headers = 'From: Your Website <ugyenchheda@gmail.com>'; 
        // Send the email
        $email_sent = wp_mail($email, $subject, $message, $headers);

        // Prepare the response to send back to the client
        if ($email) {
            $response = array(
                'success' => true,
                'message' => '<p class="mywight">Booking Reservation has been made!</p>
				We will contact you with further information. <br> Name : '.$name.' <br> Email  : '.$email.' <br> Phone  : '.$phone.' <br> No. of People  : '.$nopep.' <br> Booking Date  : '.$booking_date.'<br> Message  : '.$booking_message.'',
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Booking successful, but failed to send the email.',
            );
        }

        wp_send_json($response);
    }

    wp_send_json_error('Booking details not provided.');
}

          //html form

                          <form  id="event-booking-form" method="post">
                    <input type="hidden" name="event_id" id="event_id"  value="<?php echo get_the_ID(); ?>">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-user"></i> </span>
                            </div>
                            <input  type="text" name="booking_details[name]" id="name" value="<?php echo isset($booking_details['name']) ? esc_attr($booking_details['name']) : ''; ?>"  class="form-control" placeholder="Full name" type="text" required>
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-at"></i> </span>
                            </div>
                            <input type="email" name="booking_details[email]" id="email" value="<?php echo isset($booking_details['email']) ? esc_attr($booking_details['email']) : ''; ?>" class="form-control" placeholder="Email address" required>
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-mobile"></i> </span>
                            </div>
                            <input type="tel" name="booking_details[phone]" id="phone" value="<?php echo isset($booking_details['phone']) ? esc_attr($booking_details['phone']) : ''; ?>"  class="form-control" placeholder="Phone number" required>
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-users"></i> </span>
                            </div>
                            <input type="num" name="booking_details[nopep]" id="nopep" value="<?php echo isset($booking_details['nopep']) ? esc_attr($booking_details['nopep']) : ''; ?>" class="form-control" placeholder="Number of Visitors" required>
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-calendar-alt"></i> </span>
                            </div>
                            <input type="date" name="booking_details[booking_date]"  id="booking_date" value="<?php echo isset($booking_details['booking_date']) ? esc_attr($booking_details['booking_date']) : ''; ?>" required class="form-control" placeholder="Booking Date">
                        </div>             
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="far fa-comment"></i> </span>
                            </div>
                            <textarea type="text" name="booking_details[booking_message]"  rows="5" cols="40" id="booking_message" value="<?php echo isset($booking_details['booking_message']) ? esc_attr($booking_details['booking_message']) : ''; ?>" required class="form-control" placeholder="Want to include a message?"></textarea>
                        </div>                           
                        <div class="form-group">
                            <button type="submit" class="btn btn-defaulter btn-block"> Book Now</button>
                        </div>          
                        <span class='event-textarea'></span>                                                 
                    </form>

          // ajax javascript
              $('#event-booking-form').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var ajaxurl = my_ajax_object.ajax_url;
    
        var eventID = $('[name="event_id"]').val();
        formData += '&event_id=' + eventID;
    
        // Get the name and email from the input fields using their IDs
        var name = $('#name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var nopep = $('#nopep').val();
        var bookingnum = $('#bookingnum').val();
        var booking_message = $('#booking_message').val();
        var booking_date = $('#booking_date').val();
        var event_id = $('#event_id').val();
    
        // Add name and email as data parameters
        formData += '&name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&phone=' + encodeURIComponent(phone) 
          + '&nopep=' + encodeURIComponent(nopep) + '&bookingnum=' + encodeURIComponent(bookingnum) + '&booking_date=' + encodeURIComponent(booking_date) 
          + '&booking_message=' + encodeURIComponent(booking_message)+ '&event_id=' + encodeURIComponent(event_id);
    
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'submit_event_booking',
            formData: formData,
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {

              $('#bookingDetails').html(response.message);
    
              $('#popup1').modal('show');
              
              var bookingDetails = response.message;

              $('.event-textarea').val($('.event-textarea').val() + bookingDetails);
            } else {
              alert('Booking failed. Please try again.');
            }
          },
          error: function(xhr, status, error) {
            console.error(error);
            alert('An error occurred. Please try again later.');
          },
        });
      });
