<?php

namespace ShallowView\API\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SBPGames\Framework\Controller;
use SBPGames\Framework\JSONResponseHelper;
use SBPGames\Framework\Message\Method;
use SBPGames\Framework\Routing\Route;

/**
 * @package ShallowView\API\Controller
 * @author Xibitol <contact@pimous.dev>
 */
class APIController extends Controller{

	private const BASE_PATH = "/";

	// GETTERS
	public static function getBasePath(): string{ return self::BASE_PATH; }
	public static function getRoutes(): array{
		return [
			new Route("/^\/$/", [ Method::GET->value => "getAPI" ]),

			new Route("/^\/ekaekatai\/procrastinate$/", [
				Method::GET->value => "postProcrastinate"
			])
		];
	}

	// FUNCTIONS
	public function getAPI(
		ServerRequestInterface $request, ResponseInterface $response
	): ResponseInterface{
		$jsonRes = new JSONResponseHelper($response);

		return $jsonRes->write([
			"description" => "ShallowView dashboard public API.",
			"version" => "1.0.0-b.0",
			"authors" => [
				[
					"username" => "Xibitol",
					"url" => "https://github.com/Xibitol",
					"note" => "A developer in too many domains."
				]
			],
			"license" => [
				"name" => "LGPL-3.0-or-later",
				"url" => "https://www.gnu.org/licenses/lgpl-3.0.html"
			],
			"repository" => "https://github.com/ShallowView/WebServices",
			"documentation" => "https://doc.shallowview.org/"
		]);
	}

	public function postProcrastinate(
		ServerRequestInterface $request, ResponseInterface $response
	): ResponseInterface{
		return (new JSONResponseHelper($response))->write([
			"isProcrastinating" => true,
			"comment" => "Stop looking at this page and go back to work!"
		]);
	}
}
