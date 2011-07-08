<?php
/**
 * Copyright (c) 2011, Jeremy Brown
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * - Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Application
 * @package    Bootstrap
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

use	Symfony\Component\DependencyInjection,
    Symfony\Component\DependencyInjection\Loader,
    App\Engine\Plugin;

/**
 * Application Bootstrap
 *
 * @since      1.0
 * @category   Application
 * @package    Bootstrap
 * @subpackage Bootstrap
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Current options from bootstrap
	 * 
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Constructor
	 *
	 * Ensure FrontController resource is registered
	 *
	 * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
	 * @return void
	 */
	public function __construct($application)
	{
		parent::__construct($application);

		$this->_options = $this->getOptions();
	}

	/**
	 * Setup the application's routes
	 *
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('frontController');

		/* @var $frontController Zend_Controller_Front */
		$frontController = $this->getResource('frontController');

		// Add PUT handler controller module
		$frontController->registerPlugin(new Zend_Controller_Plugin_PutHandler());

		// BEGIN: Setup routes

		// Remove default routes
		$frontController->getRouter()->removeDefaultRoutes();

		/* @var $router Zend_Controller_Router_Rewrite */
		$router = $frontController->getRouter();

		// developer.* hostname route
		$developerHostRoute = new Zend_Controller_Router_Route_Hostname( rtrim( $this->_options['resources']['frontController']['developerUrl'], '/' ), array('module' => 'developer'));
		$plainPathRoute = new Zend_Controller_Router_Route_Module();
		$router->addRoute('developer', $developerHostRoute->chain($plainPathRoute));

		// api.* hostname route
		$apiHostRoute = new Zend_Controller_Router_Route_Hostname( preg_replace( '/\/*v.+/i', '', $frontController->getBaseUrl() ), array('module' => 'api'));

		// Format of routes in array is: URI Pattern => Controller
		$routes = array(

			/**
			 * Note: Reverse Matching
			 * Routes are matched in reverse order so make sure your most generic routes are defined first. 
			 */

			// Base API
			'/'													=> 'index_1',
			':version/'											=> 'index_2',

			// Countries
			':version/countries/*'								=> 'countries',

			// Country
			':version/country/*'								=> 'country_1',
			':version/country/:countryId/*'						=> 'country_2',
		);

		// Chain REST resource URIs to controllers
		foreach ($routes as $pattern => $controller)
		{
			$router->addRoute(
				$controller,
				$apiHostRoute->chain( new Zend_Controller_Router_Route( $pattern, array( 'module' => 'api', 'controller' => preg_replace( '/_[0-9]+/i', '', $controller ) ) ) )
			);
		}

		// END: Setup routes
	}

	/**
	 * Set the default application timezone
	 *
	 * @return void
	 */
	protected function _initTimezone()
	{
		date_default_timezone_set( 'America/Chicago' );
	}

	/**
	 * Set system locale
	 *
	 * @return void
	 */
	protected function _initSystemLocale()
	{
		$this->bootstrap('locale');
		setlocale( LC_CTYPE, $this->getResource('locale')->toString() . '.utf-8' );
	}

	/**
	 * Initialize Plugins
	 * 
	 * @return void
	 */
	protected function _initPlugins()
	{
		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontController');
		$frontController->registerPlugin( new App_Engine_Plugin_ChangeModuleLayout() );
		$frontController->registerPlugin( new App_Engine_Controller_Plugin_AcceptHandler() );
		$frontController->registerPlugin( new App_Engine_Controller_Plugin_RestRouting() );
	}

	/**
	 * Initialize Action Helpers
	 * 
	 * @return void
	 */
	protected function _initActionHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new App_Engine_Controller_Helper_Params());
	}

	/**
	 * Setup additional view parameters
	 *
	 * @return Zend_View
	 */ 
	protected function _initViewEnvironment()
	{
		$this->bootstrap('view');
		$this->bootstrap('locale');

		$view = $this->getPluginResource('view')->getView();

		$encoding = strtoupper( $this->_options['resources']['view']['encoding'] );        

		$view->setEncoding( $encoding );

		$view->headMeta()->setCharset( $encoding );
		$view->headMeta()->appendHttpEquiv( 'Content-Type', 'text/html; charset=' . $encoding );
		$view->headMeta()->appendHttpEquiv( 'Content-Language', $this->getResource('locale')->getLanguage() );
	}

	/**
	 * Setup logger
	 */
	protected function _initLog()
	{
		if ( 'development' === APPLICATION_ENV )
		{
			$writer = new Zend_Log_Writer_Firebug();
		}
		else
		{
			if ( ! empty( $this->_options['appSettings']['logger']['email'] ) )
			{
				$mail = new Zend_Mail();
				$mail->setFrom( $this->_options['appSettings']['logger']['email'] )
					->addTo( $this->_options['appSettings']['logger']['email'] );
	
				$writer = new Zend_Log_Writer_Mail($mail);
	
				// Set subject text for use; summary of number of errors is appended to the subject line before sending the message.
				$writer->setSubjectPrependText('API Application Error has occurred');
			}
			else
			{
				$writer = new Zend_Log_Writer_Null;
			}
		}

		return new Zend_Log($writer);
	}

	/**
	 * Setup a resource loader for the developer module
	 *
	 * @return void
	 */
	protected function _initDeveloperResourceLoader()
	{
		$resourceLoader = new Zend_Application_Module_Autoloader(array(
			'namespace'		=> 'Developer',
			'basePath'		=> APPLICATION_PATH . '/modules/developer',
		));
	}

	/**
	 * Setup a resource loader for the API module
	 *
	 * @return void
	 */
	protected function _initApiResourceLoader()
	{
		$resourceLoader = new Zend_Application_Module_Autoloader(array(
			'namespace'		=> 'Api',
			'basePath'		=> APPLICATION_PATH . '/modules/api',
		));
	}

	/**
	 * Configure Dependency Injection Container
	 * 
	 * @return void
	 */
	protected function _initDependencyInjectionContainer()
	{		
		// Configure DI Container
		$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
		$loader = new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container);
		$loader->load( realpath(APPLICATION_PATH . '/../config') . '/di/services.xml');

		// Store DI Container for later reuse
		Zend_Registry::set('di', $container);
	}
}
