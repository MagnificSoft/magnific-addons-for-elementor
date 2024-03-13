jQuery(document).ready(function($) {


   // wrapper link
   $('[data-mae-element-link]').each(function() {
      var link = $(this).data('mae-element-link');
      $(this).on('click', function() {
          if (link.is_external) {
              window.open(link.url);
          } else {
              location.href = link.url;
          }
      })
  });



});