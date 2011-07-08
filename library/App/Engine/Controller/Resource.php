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
 * @category   App
 * @package    Engine
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

/**
 * Abstract Resource Controller
 * 
 * This code based in part on https://github.com/mikekelly/Resauce
 *
 * @since      1.0
 * @category   App
 * @package    Api
 * @subpackage Controller
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
abstract class App_Engine_Controller_Resource extends Zend_Controller_Action
{
	/**
	 * Methods the controller allows
	 * 
	 * @var array
	 */
	protected $_allow = array(
		'GET',
		'PUT',
		'POST',
		'DELETE',
		'HEAD',
		'OPTIONS',
	);

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

		// For each allowed method enable all contexts
		foreach( $this->_allow as $action )
		{
			$contextSwitch->addActionContext( strtolower($action), $this->_contexts );
		}

		$contextSwitch->initContext();

		// END: Context Switching

		// Populate JSONP callback parameter
		$this->view->callback = $this->_getParam('callback', 'callback');
	}

	/**
	 * Provide calling client with acceptable request methods
	 * 
	 * @return void
	 */
	public function notAllowedAction()
	{
		// Disable auto-rendering
		$this->_helper->viewRenderer->setNoRender();

		// Allow
		$this->getResponse()->setHeader('Allow', strtoupper(implode(', ', $this->_allow)));

		// 501 Not Implemented
		$this->getResponse()->setHttpResponseCode(501);
	}

	public function getAction()
	{
		$this->notAllowedAction();
	}

	public function putAction()
	{
		$this->notAllowedAction();
	}

	public function postAction()
	{
		$this->notAllowedAction();
	}

	public function deleteAction()
	{
		$this->notAllowedAction();
	}

	public function headAction()
	{
		$this->notAllowedAction();
	}

	public function optionsAction()
	{
		$this->notAllowedAction();
	}

	public function __call($method, $args)
	{
		$this->notAllowedAction();
	}

	/**
	 * Generates response to OPTIONS inquiry
	 */
	protected function sendOptionsReply()
	{
		// Disable auto-rendering
		$this->_helper->viewRenderer->setNoRender();

		// Clear Content-Type
		$this->getResponse()->clearHeader('Content-Type');

		// Allow
		$this->getResponse()->setHeader('Allow', strtoupper(implode(', ', $this->_allow)));

		// 200 OK
		$this->getResponse()->setHttpResponseCode(200);
	}

	/**
	 * Generates response to HEAD inquiry
	 */
	protected function sendHeadReply()
	{
		// Disable auto-rendering
		$this->_helper->viewRenderer->setNoRender();

		// 200 OK
		$this->getResponse()->setHttpResponseCode(200);

		// Set a Vary response header based on the Accept header
		// Some insight into this can be found at http://www.subbu.org/blog/2007/12/vary-header-for-restful-applications
		$this->getResponse()->setHeader('Vary', 'Accept');
	}
}
