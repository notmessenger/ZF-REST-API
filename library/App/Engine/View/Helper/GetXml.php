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
 * Return of XML GET request
 *
 * @since      1.0
 * @category   App
 * @package    Api
 * @subpackage Helper
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class App_Engine_View_Helper_GetXml
	extends Zend_View_Helper_Abstract
{
	/**
	 * Return XML payload
	 * 
	 * @param  object|array $data
	 * @param  string $node
	 * @throws \App\Engine\Exception
	 * @return string
	 */
	public function getXml($data, $node)
	{
		// Error checking
		$exception = null;

		if ( empty( $data ) )
		{
			$exception = "Was expecting data - none provided.";
		}
		elseif ( empty( $node ) )
		{
			$exception = "Was expecting node - none provided.";
		}

		if ( null != $exception )
		{
			throw new \App\Engine\Exception($exception, 500);
		}

		// BEGIN: Build header response

		$headers = new stdClass();

		// 200 OK
		$headers->ResponseCode = 200;

		// Set a Vary response header based on the Accept header
		// Some insight into this can be found at http://www.subbu.org/blog/2007/12/vary-header-for-restful-applications
		$headers->Vary = 'Accept';

		// END: Build header response

		// BEGIN: Set response headers

		/** @var Zend_Controller_Front */
		$fc = Zend_Controller_Front::getInstance(); 
		$fc->getResponse()->setHttpResponseCode($headers->ResponseCode);
		if ( isset($headers->Vary) )
		{
			$fc->getResponse()->setHeader('Vary', $headers->Vary);
		}

		// END: Set response headers

		// BEGIN: Build XML payload

		// Construct API url
		$protocol = 'http';
		if ( ! empty($_SERVER['HTTPS']) )
		{
			$protocol .= 's';
		}

		// Construct 'self' uri
		$link_self			= new stdClass();
		$link_self->href	= $protocol . '://' . rtrim( $fc->getBaseUrl(), '/' ) . '/' . preg_replace( '/^\/v.+?\//i', '', $_SERVER['REDIRECT_URL'] );
		$link_self->rel		= 'self';

		// Construct list of links
		$links[] = $link_self;

		// Put together payload
		$xmlBuilder = new App_Engine_DOMi();
		$xmlBuilder->attachToXml($headers, 'headers');

		// if single entity
		if ( is_object($data) )
		{
			$data->{'resource_link'} = $link_self;
		}
		// if collection
		else
		{
			$xmlBuilder->attachToXml($links, 'resource_link');
		}

		$xmlBuilder->attachToXml($data, $node);

		// END: Build XML payload

		return $xmlBuilder->render();
	}
}
