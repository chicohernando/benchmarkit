jQuery('form').submit(function(e) {
  e.preventDefault();

  var formData = jQuery(this).serialize();
  jQuery.ajax({
    url: 'ajax.php',
    data: formData,
    type: 'POST',
    success: function(response) {
      jQuery('.ajax_container').fadeOut('normal', function() {
        jQuery('.ajax_container').html(response);
      }).fadeIn();
      
    },
    error: function() {
      jQuery('.ajax_container').html('An unexpected error occured');
    }
  });
});