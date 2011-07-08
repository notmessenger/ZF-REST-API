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
 * @category   Api
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

/**
 * Countries view helper
 *
 * @since      1.0
 * @category   Api
 * @package    View
 * @subpackage Helper
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class Api_View_Helper_Countries
	extends Zend_View_Helper_Abstract
{
	/**
	 * Return array of each item of the Zend_Paginator result distilled to an object respresentation
	 * 
	 * @param  App\System\Service\* $service
	 * @param  Zend_Paginator $datum
	 * @return array
	 */
	public function countries($service, $datum)
	{
		foreach( $datum as $data )
		{
			$data = $service->distillAsObject($data);

			$protocol = 'http';
			if ( ! empty($_SERVER['HTTPS']) )
			{
				$protocol .= 's';
			}

			$fc = Zend_Controller_Front::getInstance();

			$link = new stdClass();
			$link->href	= $protocol . '://' . rtrim( $fc->getRequest()->getBaseUrl(), '/' ) . '/country/' . $data->id;
			$link->rel	= 'self';

			$data->{'resource_link'} = $link;

			$container[] = $data;
		}

		return $container;
	}	
}
