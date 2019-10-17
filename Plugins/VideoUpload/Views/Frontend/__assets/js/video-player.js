if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'videoPlayer',
        selector: '[data-vimeo-video]',
        classNames: {},
        selectors: {
            insertionId: '[data-insertion-id]',
            videoId: '[data-video-id]',
            endpoint: '[data-endpoint]',
        },
        init: function () {
            let self = this;
            const vimeoBaseUrl = 'https://www.vimeo.com/';
            const vimeoOEmbedBaseUrl = 'https://vimeo.com/api/oembed.json?';
            let videoElement = document.querySelector(self.selector);
            let endpoint = videoElement.dataset.endpoint;
            let insertionId = videoElement.dataset.insertionId;
            let videoId;

            requestVideoId(insertionId).then(function (data) {
                videoId = data.vimeo_video_key;
                requestVideoPlayer(videoId);
            });

            function requestVideoId(insertionId) {
                return new Promise(function (resolve, reject) {
                    let url = endpoint + '/' + insertionId;
                    $.ajax({
                        method: 'GET',
                        url: url,
                        success: function (data) {
                            resolve(data);
                        },
                        error: function (error) {
                            reject(error);
                        }
                    });
                });
            }

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

            function appendVideoPlayer(html) {
                videoElement.innerHTML = html;
            }
        },
    });
}
