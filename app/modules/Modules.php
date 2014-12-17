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

namespace FreeForAll\Modules;

/**
 * 
 */
class Modules
{
    /**
     * @var array
     *      Stores module information.
     */
    protected static $info = NULL;
    
    /**
     * Get all available modules information.
     * 
     * @param boolean $reset
     *      (optional) if true, the information is processed,
     *      wether it is available or not.
     *      Defaults to false.
     * 
     * @return array
     */
    public static function getModulesInfo($reset = FALSE)
    {
        if (! isset(self::$info) || $reset) {
            $directoryNames = self::getModuleDirectoriesNames();
            
            foreach ($directoryNames as $directoryName) {
                $moduleName = ucfirst($directoryName);
                
                $moduleNamespace = "FreeForAll\Modules\\$moduleName";
                
                $moduleInfoFilename = MODULES_PATH. "/$directoryName/Module.php";
                $moduleInfoClassName = "$moduleNamespace\Module";
                
                // Test if the module Routes.php file exists.
                $routeFilename = MODULES_PATH . "/$directoryName/Routes.php";
                if (file_exists($routeFilename)) {
                    $routeClassName = "$moduleNamespace\Routes";
                }
                else {
                    $routeFilename = NULL;
                    $routeClassName = NULL;
                }
                
                $modulePath = MODULES_PATH . "/$directoryName";
                
                self::$info[$directoryName] = array(
                    'name' => $moduleName,
                    'namespace' => $moduleNamespace,
                    'infoFilename' => $moduleInfoFilename,
                    'infoClassName' => $moduleInfoClassName,
                    'routeFilename' => $routeFilename,
                    'routeClassName' => $routeClassName,
                    'modulePath' => $modulePath,
                );
            }
        }
        
        return self::$info;
    }
    
    
    /**
     * 
     * @param string $systemName
     * 
     * @return boolean|array
     */
    public static function getModuleInfo($systemName)
    {
        self::getModulesInfo();
        return isset(self::$info[$systemName]) ? self::$info[$systemName] : FALSE;
    }
    
    
    /**
     * Scan module directories.
     * 
     * A directory is considered a module directory if
     * it contains a "ModuleInfo.php" file.
     * 
     * @return array
     *      The list of available modules directories.
     * 
     * TODO: a ModuleInfo class must extend \FreeForAll\Utils\AbstractModuleInfoFile,
     * find an efficient way to ensure it really is. 
     */
    private static function getModuleDirectoriesNames()
    {
        $names = array();
        
        if (is_dir(MODULES_PATH)) {
            $directoryContent = scandir(MODULES_PATH);
            foreach ($directoryContent as $entry) {
                $entryPath = MODULES_PATH . '/' . $entry;
                $moduleInfoFilename = $entryPath . '/Module.php';
                
                if ($entry !== '.' && $entry !== '..' && is_dir($entryPath) && file_exists($moduleInfoFilename)) {
                    $names[] = $entry;
                }
            }
        }
        
        sort($names);
        
        return $names;
    }
    
}



