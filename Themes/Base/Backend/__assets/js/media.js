if (typeof Oforge !== 'undefined') {
    Oforge.Media = {
        open: function (options, event) {
            if (event) {
                event.preventDefault();
            }
            $.get("/backend/media/ajax", function (data) {
                $("#media-chooser").remove();
                $("body").append($(data));
                Oforge.Media._init();
            });

            this.__options = options;
            return false;
        },
        _init: function () {
            this.__query = "";
            this.__page = 1;

            this.__modalElement = $("#media-chooser");
            this.__overlay = this.__modalElement.find(".overlay");
            this.__imageContainer = this.__modalElement.find("#image-container");
            this.__searchField = this.__modalElement.find("#search-media");

            this.__modal = this.__modalElement.modal();
            this.__bindDynamicItemsClickEvents();
            this.__modalElement.find("#upload-media").on("change", this, this.__imageUploadChanged);
            this.__searchField.on("keyup", this, this.__searchChanged);

            this.__dropOverlay = this.__modalElement.find('.drop-overlay');
            this.__modalElement.find('.modal-content').on('dragover', this, function (e) {
                e.preventDefault();
                e.stopPropagation();
            }).on('dragenter', this, function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.types && e.originalEvent.dataTransfer.types.some(function (type) {
                    return type === "Files";
                })) {
                    e.data.__dropOverlay.show();
                }
            }).on('dragleave', this, function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.data.__dropOverlay.hide();
            }).on('drop', this, function (e) {
                if (e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files.length) {
                    e.preventDefault();
                    e.stopPropagation();
                    var self = e.data;
                    self.__dropOverlay.hide();
                    ([...e.originalEvent.dataTransfer.files]).forEach(function (file) {
                        var formData = new FormData();
                        formData.append('upload-media', file);
                        self.__uploadAction(self, formData);
                    });
                }
            });
        },
        __bindDynamicItemsClickEvents: function () {
            this.__modalElement.find(".media-item").on("click", this, this.__mediaClicked);
            this.__modalElement.find(".no-media-item").on("click", this, this.__noMediaClicked);
            this.__modalElement.find(".media-chooser-reset").on("click", this, this.__resetClicked);
            this.__modalElement.find(".pagination-item").on("click", this, this.__paginationClicked);
            var config = {};
            if (this.__options.config) {
                config = this.__options.config;
            }
            if (this.__options.emitter) {
                var tmpConfig = $(this.__options.emitter).data('config');
                config = $.extend({}, config, tmpConfig);
            }
            if (config.hasOwnProperty('clear') && !config.clear) {
                this.__modalElement.find(".no-media-item").hide();
            }
            if (config.hasOwnProperty('reset') && !config.reset) {
                this.__modalElement.find(".media-chooser-reset").hide();
            }
        },
        __resetClicked: function (e) {
            var self = e.data;
            if (self.__options != null) {
                if (self.__options.preview != null) {
                    var $preview = $(self.__options.preview);
                    $preview.attr('src', $preview.data('current'));
                }
                if (self.__options.target != null) {
                    var $input = $(self.__options.target);
                    $input.val($input.data('current'));
                }
                self.__modalElement.modal('hide');
                self.__modalElement.remove();
            } else {
                alert("No options given");
            }
        },
        __noMediaClicked: function (e) {
            var self = e.data;
            if (self.__options != null) {
                if (self.__options.preview != null) {
                    var $preview = $(self.__options.preview);
                    $preview.attr('src', $preview.data('placeholder'));
                }
                if (self.__options.target != null) {
                    $(self.__options.target).val('');
                }
                self.__modalElement.modal('hide');
                self.__modalElement.remove();
            } else {
                alert("No options given");
            }
        },
        __mediaClicked: function (e) {
            var target = $(this), self = e.data;
            if (self.__options != null) {
                if (self.__options.preview != null) {
                    $(self.__options.preview).attr("src", target.data("media-path"));
                }
                if (self.__options.target != null) {
                    $(self.__options.target).val(target.data("media-id"));
                } else if (self.__options.callback != null) {
                    self.__options.callback({
                        id: target.data("media-id"),
                        path: target.data("media-path"),
                        name: target.data("media-name")
                    });
                }

                self.__modalElement.modal('hide');
                self.__modalElement.remove();
            } else {
                alert("No options given");
            }
        },
        __paginationClicked: function (e) {
            var target = $(this);
            var page = parseInt(target.data("page"));
            var self = e.data;

            if (!isNaN(page) && self.__page != page) {
                self.__page = page;
                self.__reloadItems();
            }
        },
        __imageUploadChanged: function (e) {
            var file = this.files[0];
            var self = e.data;
            if (file != null) {
                self.__uploadAction(self, new FormData($('#upload-media-form')[0]))
            }
        },
        __uploadAction(self, formData) {
            self.__overlay.show();
            $.ajax({
                url: "/backend/media/ajax/upload",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData
            }).done(function () {
                self.__reloadItems();
            });
        },
        __searchChanged: function (e) {
            var self = e.data;
            if (self.timeout) {
                window.clearTimeout(self.timeout);
            }
            self.timeout = window.setTimeout(function () {
                self.__query = self.__searchField.val();
                self.__page = 1;
                self.__reloadItems();
            }, 300);
        },
        __reloadItems: function () {
            var self = this;
            self.__overlay.show();
            $.get("/backend/media/ajax/search", {query: this.__query, page: this.__page}, function (data) {
                self.__imageContainer.html(data);
                self.__bindDynamicItemsClickEvents();
                self.__overlay.hide();
            });
        }
    };

    Oforge.register({
        name: 'mediaUpload',
        selector: '[data-media-upload]',
        init: function () {
            $(this.target).each(function () {
                $(this).on('click', function (event) {
                    var $this = $(this);
                    Oforge.Media.open({
                        emitter: $this,
                        preview: $this.parent().children('img'),
                        target: $this.parent().children('input')
                    }, event)
                });
            });
        }
    });

    Oforge.register({
        name: 'mediaUploadField',
        selector: '.image-field',
        init: function () {
            $(this.target).each(function () {
                $(this).on('click', function (event) {
                    var $this = $(this);
                    Oforge.Media.open({
                        emitter: $this,
                        preview: $this.find('*[data-preview]'),
                        target: $this.find('input')
                    }, event)
                });
            });
        }
    });

}
