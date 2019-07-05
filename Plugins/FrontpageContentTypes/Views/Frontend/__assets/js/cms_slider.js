// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {
    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'cms-slider',
        selector: '[data-single-slider]',
        prevArrow: "<button class='slick-prev slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>",
        nextArrow: "<button class='slick-next slick-arrow' aria-label='Previous' type='button'><svg class='icon icon--profil'><use xlink:href='#slider'></use></svg></button>",
        init: function () {
            $(this.target).slick({
                slidesToShow: 1,
                arrows: true,
                autoplay: true,
                autoplaySpeed: 7000,
                prevArrow: this.prevArrow,
                nextArrow: this.nextArrow,
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
        }
    });
}


$(document).ready(function () {

});
