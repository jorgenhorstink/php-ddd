<?php

class AutoloaderGenerator {
	private $directories = null;
		
	private static $instance;
	const DIRECTORY_SEPARATOR = "/";
		
	protected function __construct() {}
		
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function registerDirectory($directory, $skipFirstCharacters = 0) {
		if ($this->directories === null) {
			$this->directories = array();
		}
		$this->directories[] = array($directory, $skipFirstCharacters);
	}
	
	private function getClassName($file) {
		$content = file_get_contents($file);
		
		$tokens = token_get_all($content);
		
		$namespace = '';
		$class = '';
		for ($i = 0;$i<count($tokens); $i++) {
			if ($tokens[$i][0] === T_NAMESPACE) {
				for ($j=$i+1;$j<count($tokens); $j++) {
					if ($tokens[$j][0] === T_STRING) {
	                     $namespace .= '\\'.$tokens[$j][1];
	                } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
	                     break;
	                }
				}
			}
			
			if (substr($namespace, 0, 1) === '\\') {
				$namespace = substr($namespace, 1);
			}
			
			if ($tokens[$i][0] === T_CLASS || $tokens[$i][0] === T_INTERFACE) {
				for ($j=$i+1;$j<count($tokens);$j++) {
					if ($tokens[$j] === '{') {
						$class = $tokens[$i+2][1];
					}
				}
			}
		}

		return ($namespace !== '' ? $namespace . '\\' : '') . $class;
	}
	
	public function generate() {
		$list = array();
		foreach ($this->directories as $directory) {
			$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory[0]));
			foreach ($dir as $fileinfo) {
				$file = $fileinfo->getPath() . self::DIRECTORY_SEPARATOR . $fileinfo->getFilename();
				if (strtolower($fileinfo->getExtension()) === 'php') {
					$className = $this->getClassName($file);
					
					if ($className !== "") {
						$list[] = "\t\t\t\"" . strtolower(str_replace('\\', '\\\\', $className)) . "\" => \"" . BASE_DIR . str_replace("\\", "/", substr($fileinfo->getPath(), $directory[1]) . self::DIRECTORY_SEPARATOR . $fileinfo->getFilename()) . "\"";
					}
				}
			}
		}
		echo join(",\n", $list);
	}
}
