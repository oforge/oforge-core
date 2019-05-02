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

/**
 * Class AbstractTemplateManager
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractTemplateManager extends AbstractInitializer {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $data
     *
     * @return Response
     */
    public abstract function render(Request $request, Response $response, array $data);

}


