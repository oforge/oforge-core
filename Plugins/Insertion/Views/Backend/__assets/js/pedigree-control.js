;{
    $('.pedigree__control .button:first-child').on("click", function () {
        $('.pedigree__body').addClass('position-1').removeClass('position-2 position-3');
        clearButtons();
        $(this).addClass('is--active');
    });

    $('.pedigree__control .button:nth-child(2)').on("click", function () {
        $('.pedigree__body').addClass('position-2').removeClass('position-1 position-3');
        clearButtons();
        $(this).addClass('is--active');
    });

    $('.pedigree__control .button:nth-child(3)').on("click", function () {
        $('.pedigree__body').addClass('position-3').removeClass('position-1 position-2');
        clearButtons();
        $(this).addClass('is--active');
    });

    function clearButtons() {
        $('.pedigree__control .button').removeClass('is--active');
    }
}