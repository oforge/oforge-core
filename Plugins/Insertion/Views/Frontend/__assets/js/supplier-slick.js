var prevArrow = "<button class='slick-prev slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>";
var nextArrow = "<button class='slick-next slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>";


$(document).ready(function () {
    $('.slider--supplier').slick({
        arrows: true,
        variableWidth: false,
        prevArrow: prevArrow,
        nextArrow: nextArrow,
        lazyLoad: 'progressive',
        dots: false,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 1,
                    focusOnSelect: true,
                }
            }, {
                breakpoint: 959,
                settings: {
                    slidesToShow: 1,
                    focusOnSelect: true,
                }
            }
        ]
    });
});
