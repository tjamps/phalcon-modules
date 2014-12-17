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

namespace FreeForAll;

/**
 * 
 */
class Application extends \Phalcon\Mvc\Application
{
	
    /**
     * Bootstrap the current application.
     * 
     * @return \FreeForAll\Application
     *      The current object, to allow method chaining.
     */
    public function bootstrap()
    {
        $this->includeApplicationFiles();
        $this->setLoader();
        $this->registerApplicationModules();
        $this->registerGlobalServices();
        
        return $this;
    }
    
    /**
     * Run the current application.
     * 
     * @return string
     *      The output generated by the application.
     */
    public function run()
    {
        return $this->handle()->getContent();
    }
    
    /**
     * Includes application files.
     */
    private function includeApplicationFiles()
    {
        require ROOT_PATH . '/app/bootstrap/defines.php';
        require ROOT_PATH . '/app/bootstrap/env.php';
    }
    
    /**
     * Set global loader.
     * 
     * Registers directories and namespaces
     * that are then available through the
     * whole application.
     */
    private function setLoader()
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'FreeForAll\Application\Controllers' => APP_PATH . '/controllers',
        	'FreeForAll\Application\Utils' => APP_PATH . '/utils',
        ))->register();
    }
    
    /**
     * Register global services.
     * 
     * Global services are services
     * that are available through the 
     * whole application.
     */
    private function registerGlobalServices()
    {
        $di = new \Phalcon\DI\FactoryDefault();
        
        // Disable views completely.
        $di->set('view', function(){
            $view = new \Phalcon\Mvc\View();
            
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
            
            return $view;
        }, TRUE);
        
        
        $di->set('router', function() {
        	$router = new \Phalcon\Mvc\Router(FALSE);

	    	$router->notFound(array(
	    		'namespace' => 'FreeForAll\Application\Controllers',
	    		'controller' => 'error',
	    		'action' => 'notFound',
	    	));
	    	
	    	$router->removeExtraSlashes(TRUE);
	    	
	    	// Add modules custom routes.
	    	$this->mountModulesRoutes($router);
	    	
	    	return $router;
        });
        
        $this->setDI($di);
    }
    
    /**
     * Register application modules.
     */
    private function registerApplicationModules()
    {
    	$modules = array();
    	$modulesInfo = \FreeForAll\Application\Utils\Modules::getModulesInfo();
    	 
    	foreach ($modulesInfo as $moduleName => $moduleInfo) {
    		$modules[$moduleName] = array(
    			'className' => $moduleInfo['infoClassName'],
    			'path' => $moduleInfo['infoFilename'],
    		);
    	}
    	
    	$this->registerModules($modules);
    }
    
    /**
     * Mount all modules routes available.
     * 
     * @param \Phalcon\Mvc\Router $router
     */
    private function mountModulesRoutes($router)
    {
    	$modulesInfo = \FreeForAll\Application\Utils\Modules::getModulesInfo();
    	
    	foreach ($modulesInfo as $module) {
    		if (isset($module['routeFilename'])) {
    			require $module['routeFilename'];
    			$router->mount(new $module['routeClassName']);
    		}
    	}
    }
}
