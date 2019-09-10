/* Fixes some unwanted behavior where iOS wants to open external apps when iframes are present in your page
Example Usage:
use a div with the information necessary for your iframe
    <div class="any classes will be kept in the loaded iframe"

    //selector for the javascript plugin
    data-iframe="true"

    //define the endpoint from which the autofill-data should be fetched
    data-src="url"

    //define additional attributes
    data-width="XXX%"
    data-height="XXX%"
    data-frameborder="XXX%"

    ></div>
 */
if (typeof Oforge !== 'undefined') {
    Oforge.register({
        name: 'iframeLoader',
        selector: '[data-iframe]',
        init: function () {
            var self = this;
            var selectors = {
                iframe: 'iframe'
            };

            function createIframe(iframe) {
                let iframeHtml = document.createElement(selectors.iframe);
                iframeHtml.setAttribute('src', iframe.src);
                iframeHtml.setAttribute('width', iframe.width);
                iframeHtml.setAttribute('height', iframe.height);
                iframeHtml.setAttribute('frameborder', iframe.frameborder);
                iframeHtml.classList = iframe.classList;
                return iframeHtml;
            }

            document.querySelectorAll(self.selector).forEach(function(iframeDomElement) {
                let iframe = createIframe({
                    src: iframeDomElement.dataset.src,
                    width: iframeDomElement.dataset.width,
                    height: iframeDomElement.dataset.height,
                    frameborder: iframeDomElement.dataset.frameborder,
                    classList: iframeDomElement.classList
                });


                let iframeParentDomElement = iframeDomElement.parentNode;
                iframeParentDomElement.removeChild(iframeDomElement);
                iframeParentDomElement.appendChild(iframe);
            })
        }
    });
}
