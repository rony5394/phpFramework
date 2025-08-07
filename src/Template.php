<?php
declare(strict_types=1);
namespace Rony539\PhpFramework;

use Exception;

class Template {
	static protected array $templates = [];

	static private function FindRoot(): string{
		$dir = __DIR__;
		$i = 1;
		$maxDepth = 5;

		while($dir = dirname($dir)){	
			if($i >= $maxDepth)throw new Exception("No root folder for cache was found!");
			if(
			 in_array("composer.json", scandir($dir)) ||
			 in_array(".git", scandir($dir)) ||
			 in_array("projectroot", scandir($dir))
			){
				return $dir;
			}
			$i++;
		}	

		return "";
	}

	static public function AddTemplateFromFile(string $templateName, string $filePath){
		$fileContent = file_get_contents($filePath);
		if($fileContent === false) throw new \Exception("Template file $filePath cannot be loaded!");
		Template::AddTemplate($templateName, $fileContent);
	}

	static public function AddTemplate(string $templateName, string $templateContent){
		Template::$templates[$templateName] = $templateContent;	
	}
	static public function Render(string $templateName, array $params, string $cacheDirectory){
		if(!isset(Template::$templates[$templateName]))
			throw new \Exception("Template $templateName, doesn't exists.");

		$templateContent = Template::$templates[$templateName];	
		$templateHash = hash("sha256", $templateContent);
		$compiledFolderPath = ($cacheDirectory != "AUTODETECT") ? $cacheDirectory : self::FindRoot(). "/.cache"; 
		$compiledFilePath = $compiledFolderPath . "/template_$templateHash";

		if(!is_dir($compiledFolderPath)){
			mkdir($compiledFolderPath,recursive:true);
		}

		if(!is_file($compiledFilePath)){

			$patterns = [
			    '/@@/' => '@',
			    '/@#if\s*\((.*?)\)@/s' => '<?php if ($1): ?>',

			    '/@#else@/s' => '<?php else: ?>',

			    '/@\/if@/s' => '<?php endif; ?>',

			    '/@#each\s*\((.*?)\)@/s' => '<?php foreach ($1): ?>',
	
			    '/@\/each@/s' => '<?php endforeach; ?>',

			    '/@(\$[^@]+?)@/s' => '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>',

			];

			$output = preg_replace(array_keys($patterns), array_values($patterns), $templateContent);

			$tempFile = $compiledFolderPath . "/" . uniqid(more_entropy:true) . ".tmp";
			$wasSuccessfull = file_put_contents($tempFile, $output, LOCK_EX);
			if($wasSuccessfull === false)
				throw new Exception("Could not write temporary file $tempFile for template $templateName");
			$wasSuccessfull = rename($tempFile, $compiledFilePath);
			if($wasSuccessfull == false)
				throw new Exception("Could not rename temporary file $tempFile to $compiledFilePath");

		}
		extract($params, EXTR_SKIP);
		require($compiledFilePath);

	}
}

