if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'masonry',
        selector: '[data-masonry]',
        selectors: {
            addMoreButton: '[data-addmore="true"]'
        },
        init: function () {
            var self = this;
            this.$target = $(this.target);
            this.$window = $(window);
            var imageCount = self.$target.find("img").length;

            this.$target.on("load", "img", function () {
                self.updateMasonry();
            });

            this.$window.on("load", function () {
                self.updateMasonry();
            });

            this.$window.on("resize", function () {
                self.updateMasonry();
            });

            /** After the update button has been pressed the Masonry is being updated every 500ms for a total time of 30s, if the number of images changes */
            $(this.selectors.addMoreButton).on("click", function () {
                let updateInterval = setInterval(function () {
                    let newImageCount = self.$target.find("img").length;
                    if (newImageCount > imageCount) {
                        imageCount = newImageCount;
                        self.updateMasonry();
                        clearInterval(updateInterval);
                    }
                }, 500);

                setTimeout(function () {
                    clearInterval(updateInterval);
                }, 30000);
            });

            self.updateMasonry();

        },
        updateMasonry: function () {
            var self = this;
            this.$target.each(function () {
                var $con = $(this);
                var rowGap = parseInt(window.getComputedStyle(this).getPropertyValue('grid-row-gap')),
                    rowHeight = parseInt(window.getComputedStyle(this).getPropertyValue('grid-auto-rows'));

                $con.children("div").each(function () {
                    /*
                     * Spanning for any brick = S
                     * Grid's row-gap = G
                     * Size of grid's implicitly create row-track = R
                     * Height of item content = H
                     * Net height of the item = H1 = H + G
                     * Net height of the implicit row-track = T = G + R
                     * S = H1 / T
                     */

                    var $content = $(this).find("[data-masonry-content]");
                    if ($content.length > 0) {
                        var rowSpan = Math.ceil(($content[0].getBoundingClientRect().height + rowGap) / (rowHeight + rowGap));
                        /* Set the spanning as calculated above (S) */
                        this.style.gridRowEnd = 'span ' + rowSpan;
                    }
                });
            });
        }
    });
}
