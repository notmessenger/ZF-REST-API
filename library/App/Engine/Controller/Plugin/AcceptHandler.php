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
 * AcceptHandler Controller Plugin
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
class App_Engine_Controller_Plugin_AcceptHandler
	extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		if ( !$request instanceof Zend_Controller_Request_Http)
		{
			return;
		}

		// Accept URI parameter over Accept header for specifying of desired response format
		$format = ( $this->getRequest()->getParam('format') ) ?: $request->getHeader('Accept');  

		// @todo Need to look into implementing Accept header supporting multiple types with quality factors
		switch (true)
		{
			// XML
			case (stristr($format, 'text/xml') && (!stristr($format, 'html'))):
				$request->setParam('format', 'xml');
				break;

			// JSONP/Javascript
			case (stristr($format, 'text/javascript')):
				$request->setParam('format', 'js');
				break;

			// JSON
			case (stristr($format, 'application/json')):
			default:											// Note the fall through!
				$request->setParam('format', 'json');
				break;
		}
	}
}
