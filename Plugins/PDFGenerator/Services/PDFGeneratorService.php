<?php

namespace PDFGenerator\Services;

use Mpdf\Mpdf;
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

    public function __construct() {
        $this->mpdf = new Mpdf();
        $this->templateManagementService = Oforge()->Services()->get("template.management");
        $this->templateName = $this->templateManagementService->getActiveTemplate()->getName();
        $this->templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->templateName . DIRECTORY_SEPARATOR . 'PDFTemplates';
    }

    /**
     * @param $PDFTemplate
     * @param array $templateData
     *
     * @return string
     * @throws MpdfException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_SyntaxAlias
     */
    public function generatePDF($PDFTemplate, $templateData = []) {
        $this->templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME . DIRECTORY_SEPARATOR . 'PDFTemplates';

        $twig = new CustomTwig($this->templatePath);
        $twig->addExtension(new AccessExtensionAlias());
        $twig->addExtension(new AccessExtension());
        $twig->addExtension(new MediaExtension());
        $twig->addExtension(new SlimExtension());
        $twig->addExtension(new TwigOforgeDebugExtension());

        /** @var string $html */
        $html = $twig->fetch($template = $PDFTemplate, $data = $templateData);

        /** @var InlineCssService $inlineCssService */
        $inlineCssService = Oforge()->Services()->get('inline.css');

        $this->mpdf->WriteHTML($inlineCssService->renderInlineCss($html));
        return $this->mpdf->Output('test.pdf', '');
    }
}
