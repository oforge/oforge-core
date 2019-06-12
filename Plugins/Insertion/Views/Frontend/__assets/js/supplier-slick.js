var prevArrow = "<button class='slick-prev slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>";
var nextArrow = "<button class='slick-next slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>";

$(document).ready(function () {
    $('.slider--supplier').slick({
        slidesToShow: 1,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 7000,
        prevArrow: prevArrow,
        nextArrow: nextArrow,
        lazyLoad: 'progressive',
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    autoplaySpeed: 5000
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    autoplaySpeed: 5000
                }
            }
        ]
    });
});