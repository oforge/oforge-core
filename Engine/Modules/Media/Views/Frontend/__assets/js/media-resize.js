window.setTimeout(function () {
        if (typeof Oforge !== 'undefined') {
            if (typeof Oforge.MediaResize == 'undefined') {
                Oforge.MediaResize = {
                    start: function (url, id, options) {
                        var $container = $(".media-resize-container");

                        if ($container.length > 0) {
                            $container.remove();
                        }

                        if (this.cropper != null) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }

                        this.data = {
                            url: url,
                            id: id,
                            options: options
                        };

                        if (this.data.options == null) {
                            this.data.options = {
                                processUrl: "change-image"
                            };
                        }

                        if (this.data.options.processUrl == null) {
                            this.data.options.processUrl = "change-image";
                        }

                        this.__initContainer();
                        this.__initCropper();
                    },
                    __initContainer: function () {
                        $("body").append("<div class='media-resize-container'>\n" +
                            "        <div class='media-resize-container-header'>\n" +
                            "            <a id='media-resize-discard' class='media-resize-controls cancel' data-action='discard' href='#'></a>\n" +
                            "            <a id='media-resize-accept' class='media-resize-controls accept' data-action='accept' href='#'></a>\n" +
                            "        </div>\n" +
                            "        <div class='media-resize-container-canvas'>\n" +
                            "            <img id='media-resize-container-image' crossorigin='anonymous' src='' />\n" +
                            "        </div>\n" +
                            "        <div class='media-resize-container-controls'>\n" +
                            "            <a class='media-resize-controls move' data-action='move' href='#'></a>\n" +
                            "            <a class='media-resize-controls crop' data-action='crop' href='#'></a>\n" +
                            "            <a class='media-resize-controls rotate-r' data-action='resize-left' href='#'></a>\n" +
                            "            <a class='media-resize-controls rotate-l' data-action='resize-right' href='#'></a>\n" +
                            "            <a class='media-resize-controls mirror-v' data-action='mirror-vertical' href='#'></a>\n" +
                            "            <a class='media-resize-controls mirror-h' data-action='mirror-horizontal' href='#'></a>\n" +
                            "        </div>\n" +
                            "    </div>");

                        $("#media-resize-container-image").attr("src", this.data.url);
                        var self = this;
                        $(".media-resize-controls").on("click", function (event) {
                            var $target = $(this);
                            self.__processAction($target, $target.attr("data-action"));
                        });

                    },
                    __processAction: function (target, action) {
                        switch (action) {
                            case "discard":
                                this.__close();
                                break;

                            case "accept":
                                this.__save();
                                break;

                            case "move":
                                this.cropper.setDragMode("move");
                                this.cropper.clear();
                                break;

                            case "crop":
                                this.cropper.setDragMode("move");
                                this.cropper.crop();
                                break;

                            case "resize-left":
                                this.cropper.rotate(90);
                                break;
                            case "resize-right":
                                this.cropper.rotate(-90);
                                break;
                            case "mirror-horizontal":
                                if (this.scaleX == undefined) this.scaleX = 1;
                                this.scaleX *= -1;
                                this.cropper.scaleX(this.scaleX);
                                break;
                            case "mirror-vertical":
                                if (this.scaleY == undefined) this.scaleY = 1;
                                this.scaleY *= -1;
                                this.cropper.scaleY(this.scaleY);
                                break;
                        }
                    },
                    __initCropper: function () {
                        var self = this;
                        this.cropper = new Cropper($("#media-resize-container-image").get(0), {
                            checkCrossOrigin: false,
                            cropBoxMovable: false,
                            ready: function () {
                                self.cropper.setDragMode("move");
                                self.cropper.clear();
                            }
                        });
                    },
                    __close() {
                        this.cropper.destroy();
                        $(".media-resize-container").remove();
                    },
                    __save() {
                        var croppedCanvas = this.cropper.getCroppedCanvas({maxWidth: 2048, maxHeight: 2048});
                        var self = this;
                        window.timtom = croppedCanvas;
                        croppedCanvas.toBlob((blob) => {
                            const formData = new FormData();

                            // Pass the image file name as the third parameter if necessary.
                            formData.append('files[]', blob);
                            formData.append('type', 'image/png');
                            formData.append('path', self.data.url);
                            formData.append('id', self.data.id);
                            // Use `jQuery.ajax` method for example
                            $.ajax(self.data.options.processUrl, {
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success() {
                                    self.__close();
                                },
                                error() {
                                    self.__close();
                                },
                            });
                        }, 'image/png');
                    }
                }


                Oforge.MediaResize.start("https://www.allyourhorses.de/var/public/images/a6/a9/2.jpg");
            }

        } else {
            console.warn("Oforge is not defined. Module cannot be registered.");
        }

    }
    ,
    300
);
