if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'CmsContentTypeEditorGallery',
        selector: '.gallery__editor',
        init: function () {
            var $target = $(this.target);
            $target.each(function () {
                var $galleryEditor = $(this);
                var $galleryItemWrapper = $galleryEditor.find('.gallery__items');
                var $hiddenAdd = $galleryItemWrapper.find('.gallery__item:last').detach().show();

                $galleryEditor.on('click', '.gallery__editor--add', function (event) {
                    Oforge.Media.open({
                        emitter: $(this),
                        preview: $hiddenAdd.find('img'),
                        //target: $hiddenAdd.find('input'),
                        callback: function (data) {
                            if (data && data.path) {
                                var $clone = $hiddenAdd.clone();
                                $clone.find().find('input').val(data.id);
                                $galleryItemWrapper.append($clone);
                            }
                        }
                    }, event);
                }).on('click', '.gallery__item--close', function (event) {
                    $(this).parent().remove();
                }).on('click', '.gallery__item--edit', function (event) {
                    var $self = $(this);
                    Oforge.Media.open({
                        emitter: $self,
                        preview: $self.parent().find('img'),
                        target: $self.parent().find('input')
                    }, event);
                });
                $galleryItemWrapper.sortable({
                    placeholder: "gallery__item--placeholder"
                }).disableSelection();
            });
        }
    });
}
