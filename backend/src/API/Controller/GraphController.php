<?php

namespace ShallowView\API\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SBPGames\Framework\Controller;
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
				"/^\/(?'analysis'[a-z]+)\/(?'dirname'[a-z]+(\/[a-z]+)*\/)?(?'file'[a-z]+)$/",
				[ Method::GET->value => "getAnalysis" ]
			)
		];
	}

	// FUNCTIONS
	public function getAnalysis(
		ServerRequestInterface $request, ResponseInterface $response
	): ResponseInterface{
		// Retrieves and parses query params.
		$analysis = $request->getAttribute("analysis");
		$dirname = str_replace("/", DIRECTORY_SEPARATOR,
			$request->getAttribute("dirname")
		);
		$file = $request->getAttribute("file");

		// Writes response.
		return $response
			->withStatus(Status::OK->value)
			->withHeader("Content-Type", "application/json")
			->withBody($this->getJSONFiles()->loadStream(
				$analysis.DIRECTORY_SEPARATOR.$dirname, $file
			));
	}
}