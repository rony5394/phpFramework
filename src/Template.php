<?php
declare(strict_types=1);
namespace Rony539\PhpFramework;

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
		Template::AddTemplate($templateName, file_get_contents($filePath));
	}

	static public function AddTemplate(string $templateName, string $templateContent){
		Template::$templates[$templateName] = $templateContent;	
	}
	static public function Render(string $templateName, array $params){
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

			foreach(mb_str_split($templateContent) as $char ){
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
				}

				if(str_starts_with($buffer, "#if")){
					$buffer = str_replace("#if", "if ", $buffer);
					$buffer .= ":";
				}

				if(str_starts_with($buffer, "/each"))
					$buffer = str_replace("/each", "endforeach;", $buffer);
				

				if(str_starts_with($buffer, "#else"))
					$buffer = str_replace("#else", "else:", $buffer);

				if(str_starts_with($buffer, "/if"))
					$buffer = str_replace("/if", "endif;", $buffer);

				$buffer = "<?php " . $buffer . " ?>";
				$output .= $buffer;
				$buffer = "";	
			
			}
			file_put_contents($compiledFilePath, $output);

		}
		extract($params);
		require($compiledFilePath);

	}
}

