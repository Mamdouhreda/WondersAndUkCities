$(document).ready(function () {
    $(".carousel").slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      variableWidth: true,
      centerMode: false,
      prevArrow: $("#prevBtn"),
      nextArrow: $("#nextBtn"),
    });
  });