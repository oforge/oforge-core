<?php
/*****************************************************
 *
 *      OFORGE
 *      Copyright (c) 7P.konzepte GmbH
 *      License: MIT
 *
 *
 *                (                           (
 *               ( ,)                        ( ,)
 *              ). ( )                      ). ( )
 *             (, )' (.                    (, )' (.
 *            \WWWWWWWW/                  \WWWWWWWW/
 *             '--..--'                    '--..--'
 *                }{                          }{
 *                {}                          {}
 *              _._._                       _._._
 *             _|   |_                     _|   |_
 *             | ... |_._._._._._._._._._._| ... |
 *             | ||| |  o   MUCH FORGE  o  | ||| |
 *             | """ |  """    """    """  | """ |
 *        ())  |[-|-]| [-|-]  [-|-]  [-|-] |[-|-]|  ())
 *       (())) |     |---------------------|     | (()))
 *      (())())| """ |  """    """    """  | """ |(())())
 *      (()))()|[-|-]|  :::   .-"-.   :::  |[-|-]|(()))()
 *      ()))(()|     | |~|~|  |_|_|  |~|~| |     |()))(()
 *         ||  |_____|_|_|_|__|_|_|__|_|_|_|_____|  ||
 *      ~ ~^^ @@@@@@@@@@@@@@/=======\@@@@@@@@@@@@@@ ^^~ ~
 *           ^~^~                                ~^~^
 *
 *
 *
 **********************************************************/

namespace Oforge\Engine\Modules\Core\Abstracts;

use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFlash;

/**
 * Class AbstractViewManager
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractViewManager {

    /**
     * Storage of messages and data for next request / redirect.
     *
     * @return TwigFlash
     */
    public abstract function Flash() : TwigFlash;

    /**
     * Assign data from a controller to a template
     *
     * @param array $data
     *
     * @return AbstractViewManager
     */
    public abstract function assign($data);

    /**
     * Fetch view data. This function should be called from the route middleware
     * so that it can transport the data to the TemplateEngine
     *
     * @return array
     */
    public abstract function fetch();

    /**
     * Get a specific key value from the viewData
     *
     * @param string $key
     *
     * @return mixed
     */
    public abstract function get(string $key);

    /**
     * Check if a specific key exists and is not empty
     *
     * @param string $key
     *
     * @return bool
     */
    public abstract function has(string $key) : bool;

}
