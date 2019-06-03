(function ($) {

    $('.form-single-image-uploader').each(function (index) {
        var $component = $(this);
        var $actionInput = $component.find('input[type="hidden"]');
        var $fileInput = $component.find('input[type="file"]');
        var $fileNameInput = $component.find('input.form-single-image-uploader--filename');
        var $previewWrapper = $component.find('.form-single-image-uploader--preview-wrapper');
        var $previewImage = $previewWrapper.find('img');
        var currentImage = $previewImage.data('current');
        var $btnReset = $component.find('.form-single-image-uploader--reset');
        var $btnDelete = $component.find('.form-single-image-uploader--delete');

        $fileInput.change(function () {
            var files = $fileInput[0].files;
            if (files.length === 0) {
                $previewImage[0].src = currentImage;
            } else {
                var objectURL = URL.createObjectURL(files[0]);
                var filename = files[0].name.split('.').slice(0, -1).join('.');
                $previewImage[0].src = objectURL;
                $previewWrapper.removeClass('delete');
                $actionInput.val('upload');
                $fileNameInput.val(filename).show();
                $btnReset.show();
                $btnDelete.addClass('form-single-image-uploader--disabled');
            }
        });

        $btnReset.click(function () {
            $actionInput.val('none');
            $previewWrapper.removeClass('delete');
            $fileInput.val('').change();
            if (currentImage) {
                $btnDelete.removeClass('form-single-image-uploader--disabled');
            }
            $fileNameInput.hide();
            $btnReset.hide();
        });
        $btnDelete.click(function () {
            if ($btnDelete.hasClass('form-single-image-uploader--disabled')) {
                return;
            }
            $btnDelete.addClass('form-single-image-uploader--disabled');
            $actionInput.val('delete');
            $previewWrapper.addClass('delete');
            $btnReset.show();
        });
    });

})(jQuery);
