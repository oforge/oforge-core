<?php

namespace Oforge\Engine\Modules\Core\Manager\Routes;

use Oforge\Engine\Modules\Core\Models\Endpoints\Endpoint;

class RouteMiddleware {
	protected $endpoint = null;

	public function __construct( Endpoint $endpoint ) {
		$this->endpoint = $endpoint;
	}

	/**
	 * Example middleware invokable class
	 *
	 * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
	 * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
	 * @param  callable $next Next middleware
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function __invoke( $request, $response, $next ) {
		Oforge()->View()->assign( [
			'meta' => [
				'route'             => $this->endpoint->toArray(),
				'language'          => $this->endpoint->getLanguageID(),
				'controller_method' => $this->endpoint->getController(),
				'asset_scope'       => $this->endpoint->getAssetScope(),
			],
		] );

		return $next( $request, $response );
	}
}
