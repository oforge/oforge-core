<?php
/*****************************************************
 *
 *     	OFORGE
 *      Copyright (c) 7P.konzepte GmbH
 *		License: MIT
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

use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractViewManager {
    /**
     * Assign Data from a Controller to a Template
     *
     * @param array $data
     */
    public abstract function assign($data);

    /**
     * Fetch View Data. This function should be called from the route middleware
     * so that it can transport the data to the TemplateEngine
     *
     * @return array
     */
    public abstract function fetch();
    
    /**
     * Get a specific key value from the viewData
     *
     * @param $key
     *
     * @return mixed
     */
    public abstract function get(string $key);
}
