jQuery(document).on("scroll", function() {
  var pageTop = jQuery(document).scrollTop()
  var pageBottom = pageTop + jQuery(window).height()
  var tags = jQuery(".fade")

  for (var i = 0; i < tags.length; i++) {
    var tag = tags[i]

    if (jQuery(tag).position().top < pageBottom) {
      jQuery(tag).addClass("visible")
    }
  }
})

jQuery('.navbar-burger').click(function() {
  jQuery('#navbar-mobile, .mobile, .navbar-burger').toggleClass('is-active');
});