var prevArrow = "<button class='slick-prev slick-arrow' aria-label='Previous' type='button' style=''><svg class='icon icon--profil'><use xlink:href='#pfeil'></use></svg></button>";
var nextArrow = "<button class='slick-next slick-arrow' aria-label='Previous' type='button' style=''><svg class='icon icon--profil'><use xlink:href='#pfeil'></use></svg></button>";

$(document).ready(function () {
    $('.slider').slick({
        slidesToShow: 5,
        centerMode: true,
        arrows: true,
        centerPadding: '60px',
        variableWidth: true,
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
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 1
                }
            }
        ]
    });
});
