$(function() {
  var top_nav = $('.top-nav');
  var side_nav = $('.side-nav');
  var content = $('.content');
  var menu = $('.menu');
  
  $('#tnr').metisMenu();
  $('.sn-menu').metisMenu();

  if($(window).width() > 768)
  {
    var tn_left = $('.navigation > .menu > .top-nav > .left');
    var tnl_width = $(window).width() - tn_left.innerWidth();

    var tn_right = $('.navigation > .menu > .top-nav > ul.right');

    // Width setter
    tn_right.css('width', tnl_width);
    content.css('width', tnl_width);
  }
  else
  {
    $('#toggle-nav').click(function() {
      $('#toggle-nav').toggleClass("active");

      menu.slideToggle();
    });

    content.css('width', '100%');
  }

  $(window).resize(function() {
    if($(window).width() > 768)
    {
      var tn_left = $('.navigation > .menu > .top-nav > .left');
      var tnl_width = $(window).width() - tn_left.innerWidth();

      var tn_right = $('.navigation > .menu > .top-nav > ul.right');

      // Width setter
      tn_right.css('width', tnl_width);
      content.css('width', tnl_width);

      menu.show();
    }
    else
    {
      $('#toggle-nav').click(function() {
        $('#toggle-nav').toggleClass("active");
  
        menu.slideToggle();
      });

      content.css('width', '100%');
      menu.hide();
    }
  });
});