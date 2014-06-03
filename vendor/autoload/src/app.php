<?php

require_once "AutoloaderGenerator.php";

define('CONFIG_FILE', $argv[1]);

$baseDir = $argv[2];
$baseDir = preg_replace('/[^\/]+/', '..', $baseDir);
if (substr($baseDir, -1) !== "/") {
	$baseDir .= "/";
}

define ('BASE_DIR', $baseDir);

include $argv[1];

$time = time();

echo "<" . "?" . "php";

?>


// Prevent conflicts with other class loaders
class ClassMapAutoloader<?php echo $time?> {
	private static $instance;
	public static $list;

	protected function __construct() {
		spl_autoload_register(array($this, "loadClass"));
		self::$list = array(
<?php echo AutoloaderGenerator::getInstance()->generate();?>

		);
	}

	public static function initialize() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function loadClass($className) {
		$className = strtolower($className);
		if (isset(self::$list[$className])) {
			require_once self::$list[$className];
		}
	}
}

// Make sure this 'singleton' is initialized
// It prevents executing an if statement on a getInstance singleton method :-)
ClassMapAutoloader<?php echo $time?>::initialize();