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
 * @subpackage Helper
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

/**
 * Return of JSON PUT request
 *
 * @since      1.0
 * @category   App
 * @package    Api
 * @subpackage Helper
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class App_Engine_View_Helper_PutJson
	extends Zend_View_Helper_Abstract
{
	/**
	 * Builds response to JSON PUT request
	 * 
	 * $data is the data that should be represented in the response.  In this case, it is the primary key
	 * of the updated entity.
	 * 
	 * @param object $data
	 * @param string $node
	 * @param object App\System\Service\* $service
	 * @param string $uri
	 */
	public function putJson($data = null, $node, $service =  null, $uri = null)
	{
		// Error checking
		$exception = null;

		if ( empty( $node ) )
		{
			$exception = "Was expecting node - none provided.";
		}

		if ( null != $exception )
		{
			throw new \App\Engine\Exception($exception, 500);
		}

		// Header response container
		$headers = new stdClass();

		/** @var Zend_Controller_Front */
		$fc = Zend_Controller_Front::getInstance();

		// 204 No Content
		if (
			   null == $data
			&& null == $service
			&& null == $uri
		)
		{
			$headers->ResponseCode = 204;
		}
		// 303 See Other
		else
		{
			// Error checking
			$exception = null;
	
			if ( empty( $data ) )
			{
				$exception = "Was expecting data - none provided.";
			}
			elseif ( empty( $service ) )
			{
				$exception = "Was expecting node - none provided.";
			}
			elseif ( empty( $uri ) )
			{
				$exception = "Was expecting uri - none provided.";
			}

			if ( null != $exception )
			{
				throw new \App\Engine\Exception($exception, 500);
			}

			// Construct API url
			$protocol = 'http';
			if ( ! empty($_SERVER['HTTPS']) )
			{
				$protocol .= 's';
			}
	
			$_303url = $protocol . '://' . rtrim( $fc->getBaseUrl(), '/' ) . '/' . $uri . '/' . $data;

			$headers->ResponseCode			= 303;
			$headers->{'Content-Location'}	= $_303url; 
			$headers->Location				= $_303url;
		}

		// BEGIN: Set response headers
 
		$fc->getResponse()->setHttpResponseCode($headers->ResponseCode);

		// 303 See Other
		if ( 303 == $headers->ResponseCode )
		{
			$fc->getResponse()->setHeader('Content-Location', $_303url);
			$fc->getResponse()->setHeader('Location', $_303url);
		}

		// END: Set response headers

		if ( 204 != $headers->ResponseCode )
		{
			// BEGIN: Build JSON payload
	
			// Get representation of newly created resource
			$createdResource = $service->distillAsObject( $service->getById($data) );

			// Construct 'self' uri
			$link_self	= new stdClass();
			$link_self->href	= $_303url; 
			$link_self->rel		= 'self';

			$createdResource->{'resource_link'} = $link_self;

			$response = new stdClass();
			$response->headers	= $headers;
			$response->$node	= $createdResource;
	
			$payload = new stdClass();
			$payload->response = $response;
	
			// END: Build JSON payload
	
			return Zend_Json::encode($payload);
		}
	}
}
