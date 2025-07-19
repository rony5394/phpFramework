<?php
declare(strict_types=1);
namespace Rony539\PhpFramework;

use Exception;

class Template {
	static protected array $templates = [];

	static private function FindRoot(): string{
		$dir = __DIR__;

		while($dir = dirname($dir)){	
			if(in_array("composer.json", scandir($dir)) ||
			in_array(".git", scandir($dir)))
				return $dir;
		}	

		return "";
	}

	static public function AddTemplateFromFile(string $templateName, string $filePath){
		$fileContent = file_get_contents($filePath);
		if($fileContent === false) throw new \Exception("Template file $filePath cannot be loaded!");;
		Template::AddTemplate($templateName, $fileContent);
	}

	static public function AddTemplate(string $templateName, string $templateContent){
		Template::$templates[$templateName] = $templateContent;	
	}
	static public function Render(string $templateName, array $params){
		if(!isset(Template::$templates[$templateName]))
			throw new \Exception("Template $templateName, doesn't exists.");

		$templateContent = Template::$templates[$templateName];	
		$templateHash = sha1($templateContent);
		$compiledFolderPath =  self::FindRoot(). "/.cache"; 
		$compiledFilePath = $compiledFolderPath . "/template_$templateHash";

		$buffer = "";
		$output = "";

		if(!is_dir($compiledFolderPath)){
			mkdir($compiledFolderPath);
		}

		if(!is_file($compiledFilePath)){

			$ifCount = 0;
			$eachCount = 0;
			$atCount = 0;

			$patterns = [
			    '/@@/' => '@',
			    '/@#if\s*\((.*?)\)@/s' => '<?php if ($1): ?>',

			    '/@#else@/s' => '<?php else: ?>',

			    '/@\/if@/s' => '<?php endif; ?>',

			    '/@#each\s*\((.*?)\)@/s' => '<?php foreach ($1): ?>',
	
			    '/@\/each@/s' => '<?php endforeach; ?>',

			    '/@(\$\w+)@/s' => '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>',

			];

			$output = preg_replace(array_keys($patterns), array_values($patterns), $templateContent);


			$atCount = substr_count($templateContent, "@");

			if($ifCount != 0)
				throw new \Exception("Template $templateName if - endif != 0");
			if($eachCount != 0)
				throw new \Exception("Template $templateName each - endeach != 0");
			if($atCount % 2 != 0)
				throw new \Exception("Template $templateName has unclosed tags.");

			$wasSuccessfull = file_put_contents($compiledFilePath, $output, LOCK_EX);
			if($wasSuccessfull === false)
				throw new Exception("Template $compiledFilePath cannot be writen.");

		}
		extract($params, EXTR_SKIP);
		require($compiledFilePath);

	}
}

