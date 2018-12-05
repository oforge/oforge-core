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

abstract class AbstractTemplateManager extends AbstractInitializer {
    public abstract function render(Request $request, Response $response, $data);
}


