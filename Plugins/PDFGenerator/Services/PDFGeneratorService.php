<?php

namespace PDFGenerator\Services;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\MpdfException;
use Oforge\Engine\Modules\CMS\Twig\AccessExtension as AccessExtensionAlias;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Mailer\Services\InlineCssService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\CustomTwig;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigOforgeDebugExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\AccessExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\SlimExtension;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax as Twig_Error_SyntaxAlias;

class PDFGeneratorService {

    private $mpdf = null;
    private $templateManagementService = null;
    private $templateName = "";
    private $templatePath = "";

    /**
     * PDFGeneratorService constructor.
     *
     * @throws MpdfException
     * @throws ServiceNotFoundException
     */
    public function __construct() {

    }

    /**
     *  $options = [
     *      template = "",
     *      filename = "",
     *      path = "",
     * ]
     *
     * @param array $options
     * @param array $templateData
     *
     * @return string
     * @throws MpdfException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_SyntaxAlias
     */
    public function generatePDF($options, $templateData = []) {
        $config = [];
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        if (isset($options['fontDirs'])) {
            $fontDirs = array_merge($fontDirs, $options['fontDirs']);
        }
        if (isset($options['fontData'])) {
            $fontData = array_merge($fontDirs, $options['fontData']);
        }

        $config['fontDir'] = $fontDirs;
        $config['fontdata'] = $fontData;

        if (isset($options['default_font'])) {
            $config['default_font'] = $options['default_font'];
        }

        $this->mpdf = new Mpdf($config);
        $this->templateManagementService = Oforge()->Services()->get("template.management");
        $this->templateName = $this->templateManagementService->getActiveTemplate()->getName();
        $this->templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->templateName . DIRECTORY_SEPARATOR . 'PDFTemplates';

        $twig = new CustomTwig($this->templatePath);
        $twig->addExtension(new AccessExtensionAlias());
        $twig->addExtension(new AccessExtension());
        $twig->addExtension(new MediaExtension());
        $twig->addExtension(new SlimExtension());
        $twig->addExtension(new TwigOforgeDebugExtension());

        /** @var string $html */
        $html = $twig->fetch($options['template'], $templateData);
        $header = isset($options['template_header']) ? $twig->fetch($options['template_header'], $templateData) : null;
        $footer = isset($options['template_footer']) ? $twig->fetch($options['template_footer'], $templateData) : null;

        if ($header) {
            $this->mpdf->SetHTMLHeader($header);
        }
        if ($footer) {
            $this->mpdf->SetHTMLFooter($footer);
        }

        /** @var InlineCssService $inlineCssService */
        $inlineCssService = Oforge()->Services()->get('inline.css');

        $this->mpdf->WriteHTML($inlineCssService->renderInlineCss($html));

        return $this->mpdf->Output($options['path'] .  $options['filename'], 'F');
    }
}
