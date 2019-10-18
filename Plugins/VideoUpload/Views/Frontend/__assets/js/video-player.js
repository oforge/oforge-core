if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'videoPlayer',
        selector: '[data-vimeo-video-id]',
        classNames: {},
        selectors: {
            videoId: '[data-vimeo-video-id]',
            videoIframe: '[data-vimeo-video-id] iframe',
            videoThumbnail: '[data-video-thumbnail]',
            navSlider: '.slider--detail-nav',
            forSlider: '.slider--detail-nav',
        },
        init: function () {
            let self = this;
            const vimeoBaseUrl = 'https://www.vimeo.com/';
            const vimeoOEmbedBaseUrl = 'https://vimeo.com/api/oembed.json?';
            const mobileBreakpoint = 960;
            let videoElement = document.querySelectorAll(self.selector);
            let videoThumbnail = document.querySelector(self.selectors.videoThumbnail);
            let videoId = videoElement[0].dataset.vimeoVideoId;
            let credentials;
            let isMobile = true;

            requestCredentials().then(function (data) {
                credentials = data;
                requestVideoThumbnail(videoId);
                requestVideoPlayer(videoId);
            });

            function requestVideoPlayer(videoId) {
                let params = 'url=' + encodeURIComponent(vimeoBaseUrl + videoId);
                let url = vimeoOEmbedBaseUrl + params;

                $.ajax({
                    method: 'GET',
                    url: url,
                    data: {
                        height: '500px'
                    },
                    success: function (data) {
                        appendVideoPlayer(data.html)
                    },
                    error: function (error) {
                    }
                });
            }

            function displayVideoThumbnail(sourceUrl) {
                $(self.selectors.videoThumbnail).each(function () {
                    $(this).attr({
                        'src': sourceUrl,
                        width: '100%',
                        height: 'auto'
                    });
                });
            }

            function requestVideoThumbnail(videoId) {
                var statusUrl = 'https://api.vimeo.com' + '/videos/' + videoId + '?fields=transcode.status';
                $.ajax({
                    method: 'GET',
                    url: statusUrl,
                    headers: {
                        'Authorization': 'Bearer ' + credentials.vimeo_access_token
                    },
                    success: function (data) {
                        if (data.transcode.status === 'complete') {
                            var videoUrl = 'https://api.vimeo.com' + '/videos/' + videoId + '?fields=pictures';

                            $.ajax({
                                method: 'GET',
                                url: videoUrl,
                                headers: {
                                    'Authorization': 'Bearer ' + credentials.vimeo_access_token
                                },
                                success: function (data) {
                                    displayVideoThumbnail(data.pictures.sizes[3].link);
                                }
                            })
                        } else {
                            setTimeout(function () {
                            }, 1000);
                        }
                    },
                });
            }

            function requestCredentials() {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        method: 'GET',
                        url: '/vimeo-api/credentials',
                        dataType: 'json',
                        success: function (data) {
                            resolve(data);
                        },
                        error: function (error) {
                            reject(error);
                        }
                    });
                });
            }

            function appendVideoPlayer(html) {
                videoElement.forEach(function (element) {
                    element.innerHTML = html;
                });

                if (isMobile) {
                    placeVideoInNavSlider();
                } else placeVideoInForSlider();

                $(window).on("resize", function () {
                    isMobile = checkMobileState();
                    if (isMobile) {
                        placeVideoInNavSlider();
                    } else placeVideoInForSlider();
                });
            }

            function checkMobileState() {
                let width = document.documentElement.clientWidth;
                return width < mobileBreakpoint;
            }

            function placeVideoInForSlider() {
                $(self.selectors.navSlider).find(self.selectors.videoId).each(function(){
                    $(this).hide();
                });
                $(self.selectors.forSlider).find(self.selectors.videoThumbnail).each(function(){
                    $(this).show();
                });
            }

            function placeVideoInNavSlider() {
                $(self.selectors.forSlider).find(self.selectors.videoId).each(function(){
                    $(this).show();
                });
                $(self.selectors.navSlider).find(self.selectors.videoThumbnail).each(function(){
                    $(this).hide();
                });
            }

            function swapNodes(a, b) {
                let aparent = a.parentNode;
                let asibling = a.nextSibling === b ? a : a.nextSibling;
                b.parentNode.insertBefore(a, b);
                aparent.insertBefore(b, asibling);
            }
        }
    });
}
