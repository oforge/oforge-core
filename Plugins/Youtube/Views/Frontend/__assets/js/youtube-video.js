if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'youtubeVideo',
        selector: '[data-youtube-video]',
        selectors: {
            previewContainer: '.youtube_video_preview',
            errorContainer: '.youtube_video_error',
            loadingContainer: '.loading-container',
            inputField: '[name=youtube_video]',
        },
        init: function () {
            var self = this;

            this.$target = $(this.target);

            this.__previewContainer = this.$target.find(this.selectors.previewContainer);
            this.__errorContainer = this.$target.find(this.selectors.errorContainer);
            this.__loadingElement = this.$target.find(this.selectors.loadingContainer);
            this.__inputField = this.$target.find(this.selectors.inputField);
            this.__url = this.$target.attr("data-url");
            this.__inputField.on('change keydown paste input', function (event) {
                self.onChange(event);
            });
            this.processChange();
        },
        onChange: function () {
            if (this.timeout != null) {
                window.clearTimeout(this.timeout);
                this.timeout = null;
            }

            var self = this;
            this.timeout = window.setTimeout(function () {
                self.processChange();
            }, 400);
        },
        __youtube_parser: function (url) {
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            var match = url.match(regExp);
            return (match && match[7].length == 11) ? match[7] : false;
        },
        processChange: function () {
            var self = this;
            this.timeout = null;
            var oldValue = this.__value;
            this.__value = this.__inputField.val();
            if (this.__url != null && this.__value != null && this.__value != "") {

                if (this.__value.indexOf("http") == 0) {

                    var val = this.__youtube_parser(this.__value);
                    if (val != null && val != false) {
                        this.__value = val;
                        this.__inputField.val(val);
                    }
                }

                if (oldValue != this.__value) {

                    this.__loadingElement.show();
                    $.get({
                        url: this.__url + "?videoId=" + this.__inputField.val(),
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,

                        success: function (data) {
                            self.__processedData = data;
                            self.__processData();
                        },
                    });
                }
            }
        },
        __processData: function () {
            this.__loadingElement.hide();
            if (this.__processedData != null && this.__processedData["exists"] && this.__processedData["content"] != null) {
                this.__showPreview();
            } else {
                this.__showError();
            }
        },
        __showPreview: function () {
            this.__previewContainer.empty();
            this.__previewContainer.append("<img src='" + this.__processedData["content"]["thumbnail_url"] + "' />");
            this.__errorContainer.hide();
        },
        __showError: function () {
            this.__previewContainer.empty();
            this.__errorContainer.show();
        }
    });
}
