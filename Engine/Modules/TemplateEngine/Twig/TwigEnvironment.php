<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Twig;

use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig_Template;

class TwigEnvironment extends Environment {
    private $loadedTemplates = [];
    private $loading = [];

    public function __construct(\Twig_LoaderInterface $loader, $options = []) {
        parent::__construct($loader, $options);
    }

    /**
     * Loads a template internal representation.
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name The template name
     * @param int $index The index if it is an embedded template
     *
     * @return Twig_Template A template instance representing the given template name
     * @throws Twig_Error_Loader  When the template cannot be found
     * @throws Twig_Error_Runtime When a previously generated cache is corrupted
     * @throws Twig_Error_Syntax  When an error occurred during compilation
     * @internal
     */
    public function loadTemplate($name, $index = null) {
        $cls = $mainCls = $this->getTemplateClass($name);
        if (null !== $index) {
            $cls .= '_' . $index;
        }

        $originalName = $name;

        if (isset($this->loading[$cls])) {
            $max = 100;
            $i   = 1;
            do {
                $name = $originalName . "::" . $i++;

                if ($this->getLoader()->exists($name)) {
                    $cls = $mainCls = $this->getTemplateClass($name);
                    if (isset($cls) && !isset($this->loading[$cls])) {
                        break;
                    }
                } else {
                    break;
                }
            } while ($i < $max);
        }

        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            $key = $this->getCache(false)->generateKey($name, $mainCls);

            if (!$this->isAutoReload() || $this->isTemplateFresh($name, $this->getCache(false)->getTimestamp($key))) {
                $this->getCache(false)->load($key);
            }

            if (!class_exists($cls, false)) {
                $source  = $this->getLoader()->getSourceContext($name);
                $content = $this->compileSource($source);
                $this->getCache(false)->write($key, $content);
                $this->getCache(false)->load($key);

                if (!class_exists($mainCls, false)) {
                    /* Last line of defense if either $this->bcWriteCacheFile was used,
                     * $this->cache is implemented as a no-op or we have a race condition
                     * where the cache was cleared between the above calls to write to and load from
                     * the cache.
                     */
                    eval('?>' . $content);
                }

                if (!class_exists($cls, false)) {
                    throw new \Twig_Error_Runtime(sprintf('Failed to load Twig template "%s", index "%s": cache is corrupted.', $name, $index), -1, $source);
                }
            }
        }

        $this->initExtensionSet();

        if (isset($this->loading[$cls])) {
            throw new \Twig_Error_Runtime(sprintf('Circular reference detected for Twig template "%s", path: %s.', $name,
                implode(' -> ', array_merge($this->loading, [$name]))));
        }

        $this->loading[$cls] = $name;

        try {
            $this->loadedTemplates[$cls] = new $cls($this);
        } finally {
            unset($this->loading[$cls]);
        }

        return $this->loadedTemplates[$cls];
    }
}
