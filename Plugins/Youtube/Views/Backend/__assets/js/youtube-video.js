if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'youtubeVideo',
        selector: '[data-youtube-video]',
        selectors: {
            previewContainer: '.youtube_video_preview',
            loadingContainer: '.loading-container',
            inputField: 'input.form__input',
            deleteField: '.delete',
            baseField: '.video-entry.base',
        },
        timeout: [],
        __value: [],
        __index: [],
        __processedData: [],
        init: function () {
            var self = this;

            this.$target = $(this.target);
            this.__url = this.$target.attr("data-url");
            this.__baseElement = this.$target.find(this.selectors.baseField);

            this.__initEvents();

            this.processChange();
        },
        __initEvents: function () {
            var self = this;
            this.__previewContainer = this.$target.find(this.selectors.previewContainer);
            this.__loadingElement = this.$target.find(this.selectors.loadingContainer);
            this.__inputField = this.$target.find(this.selectors.inputField);
            this.__deleteFields = this.$target.find(this.selectors.deleteField);
            this.__deleteFields.off();
            this.__inputField.off();

            this.__deleteFields.on('click', function (event) {
                self.deleteElement(event, this);
            });

            this.__inputField.on('change keydown paste input', function (event) {
                self.onChange(event, this);
            });
        },
        deleteElement: function (event, target) {
            var $target = $(target);
            var elementContainer = $target.parent();
            elementContainer.remove();
            this.__initEvents();
        },
        onChange: function (event, target) {
            var $target = $(target);
            var elementContainer = $target.parent();
            var id = elementContainer.parent().children(".video-entry").index(elementContainer);

            if (this.timeout[id] != null) {
                window.clearTimeout(this.timeout[id]);
                this.timeout[id] = null;
            }

            var self = this;
            this.timeout[id] = window.setTimeout(function () {
                self.processChange(id);
            }, 400);
        },
        __youtube_parser: function (url) {
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            var match = url.match(regExp);
            return (match && match[7].length == 11) ? match[7] : false;
        },
        processChange: function (id) {
            var self = this;
            this.timeout[id] = null;
            var oldValue = this.__value[id];
            this.__value[id] = this.__inputField.eq(id).val();
            if (this.__url != null && this.__value[id] != null && this.__value[id] != "") {

                if (this.__value[id].indexOf("http") == 0) {

                    var val = this.__youtube_parser(this.__value[id]);
                    if (val != null && val != false) {
                        this.__value[id] = val;
                        this.__inputField.eq(id).val(val);
                    }
                }

                this.__index[this.__value[id]] = id;

                if (oldValue != this.__value[id]) {
                    this.__loadingElement.eq(id).show();
                    $.get({
                        url: this.__url + "?videoId=" + this.__inputField.eq(id).val(),
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        context: {'id': id},
                        success: function (data) {
                            self.__processedData[this.id] = data;
                            self.__processData(this.id);
                        },
                    });
                }
            }
        },
        __processData: function (id) {
            this.__loadingElement.eq(id).hide();
            console.log(id, this);
            if (this.__processedData[id] != null && this.__processedData[id]["exists"] && this.__processedData[id]["content"] != null) {
                this.__showPreview(id);
                var thumbnailInput = this.__loadingElement.eq(id).parent().children(".thumbnail-input");
                thumbnailInput.val(this.__processedData[id]["content"]["thumbnail_url"]);
                thumbnailInput.attr("name", "youtube_thumbnail[" + this.__inputField.eq(id).val() + "]");

                this.__addNewElement();
            } else {
                this.__showError(id);
            }
        },
        __showPreview: function (id) {
            this.__previewContainer.eq(id).empty();
            this.__previewContainer.eq(id).parent().removeClass("not-found");
            this.__deleteFields.eq(id).show();
            this.__previewContainer.eq(id).append("<img src='" + this.__processedData[id]["content"]["thumbnail_url"] + "' />");
        },
        __showError: function (id) {
            this.__previewContainer.eq(id).empty();
            this.__previewContainer.eq(id).parent().addClass("not-found");
            var thumbnailInput = this.__loadingElement.eq(id).parent().children(".thumbnail-input");
            thumbnailInput.val('');
            thumbnailInput.attr("name", "youtube_thumbnail[]");
        },
        __addNewElement: function () {
            var clone = this.__baseElement.clone();
            clone.removeClass("base");
            clone.appendTo(this.$target);
            this.__initEvents();
        }
    });
}
