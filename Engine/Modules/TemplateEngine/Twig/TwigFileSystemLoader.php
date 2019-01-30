<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Twig;

use Twig\Loader\FilesystemLoader;
use Twig_Error_Loader;

class TwigFileSystemLoader extends FilesystemLoader {
    /**
     * Checks if the template can be found.
     *
     * @param string $name The template name
     * @param bool $throw Whether to throw an exception when an error occurs
     *
     * @return false|string The template name or false
     * @throws Twig_Error_Loader
     */
    public function findTemplate($name, $throw = true) {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return false;
            }
            throw new Twig_Error_Loader($this->errorCache[$name]);
        }
        $this->validateName($name);
        list($namespace, $shortname) = $this->parseName($name);

        if (!isset($this->paths[$namespace])) {
            $this->errorCache[$name] = sprintf('There are no registered paths for namespace "%s".', $namespace);

            if (!$throw) {
                return false;
            }
            throw new Twig_Error_Loader($this->errorCache[$name]);
        }

        $i = 0;
        foreach ($this->paths[$namespace] as $path) {
            if (!$this->isAbsolutePath($path)) {
                $path = ROOT_PATH . DIRECTORY_SEPARATOR . $path;
            }

            if (is_file($path . '/' . $shortname)) {
                if (false !== $realpath = realpath($path . '/' . $shortname)) {
                    //add index for next file with same name
                    $this->cache[$name . ($i == 0 ? "" : ("::" . $i))] = $realpath;
                }
                //add index for next file with same name
                $this->cache[$name . ($i == 0 ? "" : ("::" . $i))] = $path . '/' . $shortname;
                $i++;
            }
        }

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $this->errorCache[$name] = sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[$namespace]));

        if (!$throw) {
            return false;
        }

        throw new Twig_Error_Loader($this->errorCache[$name]);
    }

    /**
     * @param $name
     *
     * @return string|string[]|null
     */
    private function normalizeName($name) {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
    }

    /**
     * @param $name
     * @param string $default
     *
     * @return array
     * @throws Twig_Error_Loader
     */
    private function parseName($name, $default = self::MAIN_NAMESPACE) {
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new Twig_Error_Loader(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }
            $namespace = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return [$namespace, $shortname];
        }

        return [$default, $name];
    }

    /**
     * @param $name
     *
     * @throws Twig_Error_Loader
     */
    private function validateName($name) {
        if (false !== strpos($name, "\0")) {
            throw new Twig_Error_Loader('A template name cannot contain NUL bytes.');
        }

        $name  = ltrim($name, '/');
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new Twig_Error_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }

    /**
     * @param $file
     *
     * @return bool
     */
    private function isAbsolutePath($file) {
        return strspn($file, '/\\', 0, 1)
               || (strlen($file) > 3 && ctype_alpha($file[0])
                   && ':' === $file[1]
                   && strspn($file, '/\\', 2, 1))
               || null !== parse_url($file, PHP_URL_SCHEME);
    }
}
