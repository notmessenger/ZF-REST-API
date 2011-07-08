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

use App\Engine;

/**
 * Params Action Helper
 * 
 * This code based in part on http://weierophinney.net/matthew/archives/233-Responding-to-Different-Content-Types-in-RESTful-ZF-Apps.html
 *
 * @since      1.0
 * @category   App
 * @package    Api
 * @subpackage Controller
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class App_Engine_Controller_Helper_Params
	extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Parameters detected in raw content body
	 * 
	 * @var array
	 */
	protected $_bodyParams = array();

	/**
	 * Do detection of content type, and retrieve parameters from raw body if present
	 *
	 * @return void
	 */
	public function init()
	{
		// Allows for \App\Engine\Exception to be thrown AND caught by default error controller
		// For full explanation, see: http://stackoverflow.com/questions/4076578/do-action-helpers-with-hooks-to-auto-run-throw-exceptions-or-not
		if( ($this->getRequest()->getActionName() == 'error') && ($this->getRequest()->getControllerName() == 'error')) { return; }

		$request     = $this->getRequest();
		$contentType = $request->getHeader('Content-Type');
		$rawBody     = $request->getRawBody();

		if (!$rawBody)
		{
			return;
		}

		switch (true)
		{
			case (stristr($contentType, 'application/json')):
				$data = Zend_Json::decode($rawBody);
				$this->setBodyParams($data['request']);
				break;

			case (stristr($contentType, 'application/xml')):
				try
				{
					$config = new Zend_Config_Xml($rawBody);	
				}
				catch (Exception $e)
				{
					$error = substr($e, 0, stripos($e, "\n") );
					throw new \App\Engine\Exception('Malformed XML: ' . $error, 400);
				}

				$this->setBodyParams($config->toArray());
				break;

			case (stristr($contentType, 'application/x-www-form-urlencoded')):
				if ($request->isPut())
				{
					parse_str($rawBody, $params);
					$this->setBodyParams($params);
				}
				break;

			default:
				throw new \App\Engine\Exception('Unsupported Content-Type provided.', 501);

				// http://trac.agavi.org/browser/tags/1.0.2/src/request/AgaviWebRequest.class.php
				// use above to possibly handle files sent via post/put
				break;
		}
	}

	/**
	 * Set body params
	 *
	 * @param  array $params
	 * @return AM_Engine_Controller_Helper_Params
	 */
	public function setBodyParams(array $params)
	{
		$changedParams = array();

		foreach($params as $key => $value)
		{
			//if ($value instanceof SimpleXMLElement) {
			$changedParams[strtolower($key)] = $value;
		}

		$this->_bodyParams = $changedParams;

		// By doing the following line, make the data availabe via $this->_request->getPost(), rendering the direct() method, and others, useless
		$this->getRequest()->setPost($changedParams);

		return $this;
	}

	/**
	 * Retrieve body parameters
	 *
	 * @return array
	 */
	public function getBodyParams()
	{
		return $this->_bodyParams;
	}

	/**
	 * Get body parameter
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function getBodyParam($name)
	{
		if ($this->hasBodyParam($name))
		{
			return $this->_bodyParams[$name];
		}

		return null;
	}

	/**
	 * Is the given body parameter set?
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function hasBodyParam($name)
	{
		if (isset($this->_bodyParams[$name]))
		{
			return true;
		}

		return false;
    }

	/**
	 * Do we have any body parameters?
	 *
	 * @return bool
	 */
	public function hasBodyParams()
	{
		if (!empty($this->_bodyParams))
		{
			return true;
		}

		return false;
    }

	/**
	 * Get submit parameters
	 *
	 * @return array
	 */
	public function getSubmitParams()
	{
		if ($this->hasBodyParams())
		{
			return $this->getBodyParams();
		}

		return $this->getRequest()->getPost();
	}

	public function direct()
	{
		return $this->getSubmitParams();
	}
}
