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
abstract class AbstractModuleInfoFile implements ModuleInfoFileInterface
{
	
	/**
	 * Get module configuration.
	 * 
	 * @param string $reset
	 * 		(optional).
	 * 
	 * @return null|\Phalcon\Config\Adapter\Ini
	 */
	public function getConfig($reset = FALSE)
	{
		static $config = NULL;
		
		if (! isset($config) || $reset) {
			// Check if module configuration exists.
			$configurationFilename = sprintf('%s/config/config.ini', MODULES_PATH . '/' . $this->getSystemName());
			
			if (file_exists($configurationFilename)) {
				$config = new \Phalcon\Config\Adapter\Ini($configurationFilename);
				
				// Check then if a specific environment configuration file is available.
				$envConfigurationFilename = sprintf('%s/config/config-%s.ini', MODULES_PATH . '/' . $this->getSystemName(), APPLICATION_ENV);
				
				if (file_exists($envConfigurationFilename)) {
					$envConfig = new \Phalcon\Config\Adapter\Ini($envConfigurationFilename);
					$config->merge($envConfig);
				}
			}
		}
		
		return $config;
	}
	
	/**
	 * Registers common module namespaces.
	 */
	public function registerCommonNamespaces()
	{
		$systemName = $this->getSystemName();
		$info = Modules::getModuleInfo($systemName);
		
		if ($info) {
			$config = $this->getConfig();
			$moduleName = $info['name'];
			$modulePath = $info['modulePath'];
			
			$loader = new \Phalcon\Loader();
			
			$loader->registerNamespaces(array(
				"FreeForAll\Modules\\$moduleName\Controllers" => $modulePath . '/' . $config->{$systemName}->controllersDir,
				"FreeForAll\Modules\\$moduleName\Models"      => $modulePath . '/' . $config->{$systemName}->modelsDir,
				"FreeForAll\Modules\\$moduleName\Exceptions"  => $modulePath . '/' . $config->{$systemName}->exceptionsDir,
			))->register();
		}
		
	}
}


