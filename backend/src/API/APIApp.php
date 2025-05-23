<?php

namespace ShallowView\API;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SBPGames\Framework\App;
use SBPGames\Framework\WebSystem;
use ShallowView\API\Controller\APIController;
use ShallowView\API\Controller\GraphController;

/**
 * @package ShallowView\API
 * @author Xibitol <contact@pimous.dev>
 */
class APIApp extends App{

	private const REGISTERED_NAME = "api.shallowview.org";
	private const IS_WWW = false;

	public function __construct(WebSystem $context){
		parent::__construct($context, [
			APIController::class,
			GraphController::class
		]);
	}

	// GETTERS
	public static function getRegName(): string{ return self::REGISTERED_NAME; }
	public static function isWWW(): bool{ return self::IS_WWW; }

	// FUNCTIONS
	public function processRequest(
		ServerRequestInterface $request, ResponseInterface $response
	): ResponseInterface{
		return parent::processRequest($request,
			$response->withHeader("Access-Control-Allow-Origin", "*")
		);
	}
}
