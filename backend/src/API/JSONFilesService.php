<?php

namespace ShallowView\API;

use Psr\Http\Message\StreamInterface;
use SBPGames\Framework\Message\FileStream;
use SBPGames\Framework\Service\Service;

/**
 * @package ShallowView\API
 * @author Xibitol <contact@pimous.dev>
 */
class JSONFilesService extends Service{

	private const SERVICE_IDENTIFIER = "jsonFiles";
	private const MANDATORY_CONFIG_FIELDS = ["directory"];

	private const JSON_FILE_FORMAT = "%s/%s/%s/%s.json";

	private readonly string $projectPath;
	private readonly string $directory;

	public function __construct(string $projectPath){
		parent::__construct(self::SERVICE_IDENTIFIER);

		$this->setProjectPath($projectPath);
	}


	// GETTERS
	public function getMandatoryConfigFields(): array{
		return self::MANDATORY_CONFIG_FIELDS;
	}
	public function getConfigFields(): array{
		return self::MANDATORY_CONFIG_FIELDS;
	}

	private function getProjectPath(): string{ return $this->projectPath; }
	private function getDirectory(): string{
		return $this->directory;
	}

	private function getDestName(string $path, string $name): string{
		return sprintf(self::JSON_FILE_FORMAT,
			$this->projectPath, $this->getDirectory(),
			rtrim($path, DIRECTORY_SEPARATOR), $name
		);
	}

	// SETTERS
	private function setProjectPath(string $projectPath): void{
		if(!is_dir($projectPath))
			throw new \UnexpectedValueException(
				"No such project directory (Got $projectPath);"
			);

		$this->projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR);
	}
	private function setDirectory(string $directory): void{
		$directory = trim($directory, DIRECTORY_SEPARATOR);
		$dir = $this->getProjectPath().DIRECTORY_SEPARATOR.$directory;

		if(!is_dir($dir) || !is_readable($dir))
			throw new \UnexpectedValueException(sprintf(
				"%s: No such JSON file directory or isn't readable (Got %s);",
				static::class, $dir
			));

		$this->directory = $directory;
	}

	// FUNCTIONS
	public function loadStream(string $path, string $name): StreamInterface{
		$file = $this->getDestName($path, $name);

		if(!is_file($file) || !is_readable($file))
			throw new \UnexpectedValueException(sprintf(
				"%s: No such JSON file or isn't readable (%s);",
				static::class, $file
			));

		return new FileStream($file, "r");
	}

	// LIFECYCLE FUNCTIONS
	public function init(array $config): void{
		parent::init($config);

		// Setting properties.
		$this->setDirectory($config["directory"]);
	}
}