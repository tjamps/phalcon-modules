<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Rémi T'JAMPENS
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace FreeForAll\Application\Utils;

/**
 * 
 */
class Modules
{
	/**
	 * @var array
	 * 		Stores module information.
	 */
	static $info = NULL;
	
	/**
	 * Get all available modules information.
	 * 
	 * @param boolean $reset
	 * 		(optional) if true, the information is processed,
	 * 		wether it is available or not.
	 * 		Defaults to false.
	 * 
	 * @return array
	 */
	public static function getModulesInfo($reset = FALSE)
	{
		if (! isset(self::$info) || $reset) {
			$directoryNames = self::getModuleDirectoriesNames();
			
			foreach ($directoryNames as $directoryName) {
				$moduleName = ucfirst($directoryName);
				
				$moduleNamespace = 'FreeForAll\Modules\\' . $moduleName;
				
				$moduleInfoFilename = MODULES_PATH. '/' . $directoryName . '/ModuleInfo.php';
				$moduleInfoClassName = $moduleNamespace . '\ModuleInfo';
				
				// Test if the module Routes.php file exists.
				$routeFilename = MODULES_PATH . '/' . $directoryName . '/Routes.php';
				if (file_exists($routeFilename)) {
					$routeClassName = $moduleNamespace . '\Routes';
				}
				else {
					$routeFilename = NULL;
					$routeClassName = NULL;
				}
				// TODO: Test if the module config/config.ini file exists.
				
				self::$info[$directoryName] = array(
					'name' => $moduleName,
					'namespace' => $moduleNamespace,
					'infoFilename' => $moduleInfoFilename,
					'infoClassName' => $moduleInfoClassName,
					'routeFilename' => $routeFilename,
					'routeClassName' => $routeClassName,
				);
			}
		}
		
		return self::$info;
	}
	
	
	/**
	 * Scan module directories.
	 * 
	 * A directory is considered a module directory if
	 * it contains a "ModuleInfo.php" file.
	 * 
	 * @return array
	 */
	private static function getModuleDirectoriesNames()
	{
		$names = array();
		
		if (is_dir(MODULES_PATH)) {
			$directoryContent = scandir(MODULES_PATH);
			foreach ($directoryContent as $entry) {
				$entryPath = MODULES_PATH . '/' . $entry;
				$moduleInfoFilename = $entryPath . '/ModuleInfo.php';
				
				if ($entry !== '.' && $entry !== '..' && is_dir($entryPath) && file_exists($moduleInfoFilename)) {
					$names[] = $entry;
				}
			}
		}
		
		sort($names);
		
		return $names;
	}
	
}



