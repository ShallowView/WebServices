<?php

namespace ShallowView\API\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SBPGames\Framework\Controller;
use SBPGames\Framework\JSONResponseHelper;
use SBPGames\Framework\Message\Method;
use SBPGames\Framework\Message\Status;
use SBPGames\Framework\Routing\Route;
use ShallowView\API\JSONFilesService;

/**
 * @package ShallowView\API\Controller
 * @author Xibitol <contact@pimous.dev>
 */
class GraphController extends Controller{

	private const BASE_PATH = "/graph";

	private JSONFilesService $jsonFiles;

	public function __construct(JSONFilesService $jsonFiles){
		$this->jsonFiles = $jsonFiles;
	}

	public function getJSONFiles(): JSONFilesService{ return $this->jsonFiles; }

	// GETTERS
	public static function getBasePath(): string{ return self::BASE_PATH; }
	public static function getRoutes(): array{
		return [
			new Route(
				"/^\/(?'analysis'[a-z]+)\/(?'type'[a-z]+)\/(?'file'[a-z]+)$/",
				[ Method::GET->value => "getAnalysis" ]
			)
		];
	}

	// FUNCTIONS
	public function getAnalysis(
		ServerRequestInterface $request, ResponseInterface $response
	): ResponseInterface{
		// Retrieves and parses query params.
		$path = implode(DIRECTORY_SEPARATOR, [
			$request->getAttribute("analysis"),
			$request->getAttribute("type")
		]);
		$file = $request->getAttribute("file");

		// Writes response.
		try{
			return $response
				->withStatus(Status::OK->value)
				->withHeader("Content-Type", "application/json")
				->withBody($this->getJSONFiles()->loadStream($path, $file));
		}catch(\UnexpectedValueException $_){
			return (new JSONResponseHelper($response))->writeError(
				Status::NOT_FOUND,
				"No such graph.",
				"about:blank"
			);
		}
	}
}