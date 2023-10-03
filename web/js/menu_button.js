$(function() {
    $('.button').click(function(){
    $(this).toggleClass('active');
    $('.sidenav').toggleClass('view_menu');
    });
  });
  $(function() {  
    $('.button').click(function () {　  
      if ($(this).hasClass("active")) {  
        $("html").addClass("no-scroll");  
      } else {                              
        $("html").removeClass("no-scroll");
      }
    });
  });