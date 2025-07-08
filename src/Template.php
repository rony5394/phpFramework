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

			for($i = 0; $i < mb_strlen($templateContent); $i++){
				$char = mb_substr($templateContent, $i, 1);
				$buffer .= $char;

				if(!str_starts_with($buffer, "@")){
					$output .= $buffer;
					$buffer = "";
					continue;
				}

				if($buffer == "@@"){
					$buffer = "";
					$output .= "@";
					continue;
				}

				if(strlen($buffer) < 3 || !str_ends_with($buffer, "@"))continue;
				$buffer = substr($buffer, 1, -1);

				if(str_starts_with($buffer, "$")){
					$buffer = 'echo htmlspecialchars('.$buffer.', ENT_QUOTES, "UTF-8");';
				}

				if(str_starts_with($buffer, "#each")){
					$buffer = str_replace("#each", "foreach ", $buffer);
					$buffer .= ":";
					$eachCount ++;
				}

				if(str_starts_with($buffer, "#if")){
					$buffer = str_replace("#if", "if ", $buffer);
					$buffer .= ":";
					$ifCount ++;
				}

				if(str_starts_with($buffer, "/each")){
					$buffer = str_replace("/each", "endforeach;", $buffer);
					$eachCount --;
				}
				

				if(str_starts_with($buffer, "#else"))
					$buffer = str_replace("#else", "else:", $buffer);

				if(str_starts_with($buffer, "/if")){
					$buffer = str_replace("/if", "endif;", $buffer);
					$ifCount --;
				}

				$buffer = "<?php " . $buffer . " ?>";
				$output .= $buffer;
				$buffer = "";	
			
			}
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

