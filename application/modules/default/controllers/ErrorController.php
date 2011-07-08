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
 * @package    Default
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

/**
 * Default Error Controller
 *
 * @since      1.0
 * @category   Application
 * @package    Default
 * @subpackage Controller
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class ErrorController extends Zend_Controller_Action
{
	/**
	 * Context types supported
	 * 
	 * @var array
	 */
	protected $_contexts = array(
		'xml',
		'json',
		'js',
	);

	/**
	 * Setup Context Switching
	 * Disable Layout rendering
	 * Push 'callback' parameter to view
	 * 
	 * @return void
	 */
	public function init()
	{
		// Only do context switching for API module
		if( 'api' != $this->getRequest()->getModuleName() ) { return; }

		// Disables layout (rendering)
		$this->_helper->layout()->disableLayout();

		// BEGIN: Context Switching
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->setAutoJsonSerialization(false);

		// Add JSONP/Javascript context
		$contextSwitch->setContext(
			'js',
			array(
				'suffix'	=> 'js',
				'headers'	=> array(
					'Content-Type' => 'text/javascript;charset=UTF-8',
				),
			)
		);

		// For each supported context type, enable action context
		foreach( $this->_contexts as $context )
		{
			$contextSwitch->addActionContext( 'error', $context );
		}

		$contextSwitch->initContext();

		// END: Context Switching

		// Populate JSONP callback parameter
		$this->view->callback = $this->_getParam('callback');
	}

	/**
	 * Default action called for error handler
	 */
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');

		// Populate JSONP callback parameter
		$this->view->callback = $this->_getParam('callback', 'callback');

		switch ($errors->type)
		{
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);

				if(
					   'api' != $this->getRequest()->getModuleName()
					&& 'development' == APPLICATION_ENV
				)
				{
					$this->view->errorMessage	= 'An Error has occurred';
					$this->view->explanation	= $errors->exception->getMessage();
					$this->view->error			= $errors;
				}
				else
				{
					// Disable auto-rendering
					$this->_helper->viewRenderer->setNoRender();
				}

				break;

			// API-specific error
			case ($errors->exception instanceof App\Engine\Exception):
				$this->getResponse()->setHttpResponseCode( ( ! $errors->exception->getCode() ) ? 500 : $errors->exception->getCode() );

				$this->view->responseCode	= ( ! $errors->exception->getCode() ) ? 500 : $errors->exception->getCode();
				$this->view->errorMessage	= 'An Error has occurred';
				$this->view->explanation	= $errors->exception->getMessage();

				// Log exception, if logger available
				if ($log = $this->getLog())
				{
					if ( '500' == $this->view->responseCode )
					{
						$log->err( print_r( $errors->exception, 1 ) );
					}
				}

				break;

			// Application error
			default:
				$this->getResponse()->setHttpResponseCode(500);

				$this->view->responseCode	= 500;
				$this->view->errorMessage	= 'An Application Error has occurred.  Tech support has been notified of this situation.';
				$this->view->explanation	= $errors->exception->getMessage();

				// Log exception, if logger available
				if ($log = $this->getLog())
				{
					$log->emerg( print_r( $errors->exception, 1 ) );
				}

				break;
		}

		// Conditionally display exceptions
		if ( true == $this->getInvokeArg('displayExceptions') ){}
	}

	/**
	 * Return logger if configured and enabled
	 * 
	 * @return false|object Zend_Log
	 */
	public function getLog()
	{
		$bootstrap = $this->getInvokeArg('bootstrap');

		if ( ! $bootstrap->hasResource('Log') )
		{
			return false;
		}

		return $bootstrap->getResource('Log');
	}
}
