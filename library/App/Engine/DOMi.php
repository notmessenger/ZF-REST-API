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
 * @subpackage DOMi
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

require_once realpath(APPLICATION_PATH . '/../library/DOMi') . '/domi.class.php';

/**
 * Customized configuration for DOMi class/library
 *
 * @since      1.0
 * @category   App
 * @package    Api
 * @subpackage DOMi
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class App_Engine_DOMi
	extends DOMi
{
	/**
	 * Create an instance of the DOMi object
	 * 
	 * @param string The name that will be used on the root node
	 * @param string character encoding for the DOMi object, ie UTF-8
	 */
	public function __construct($mainNodeName='response', $encoding='UTF-8')
	{
		if ( self::isValidPrefix($mainNodeName) )
		{
			$this->encoding = $encoding;
			$this->dom = new DOMDocument('1.0', $this->encoding);
			$this->mainNode = $this->createElement($mainNodeName);
			$this->appendChild($this->mainNode);
		}
        else
        {
			throw new Exception( "Invalid prefix '$mainNodeName'" );
		}
	}

	/**
	 * Process and return the output that will be sent to screen during the display process
	 * 
     * @param  mixed A string or array listing the XSL stylesheets to be used for the rendering process
     * @param  int A flag indicating the rendering type. Acceptable values are DOMi::RENDER_HTML and DOMi::RENDER_XML
	 * @return string The result of the processing based on the rendering mode
	 */
	public function render($stylesheets=false, $mode=self::RENDER_XML)
	{
		return $this->generateOutput($mode);
	}

	/**
	 * Have made no changes to this method.  Is included as a work-around to being marked 'private'
	 * 
	 * @param int A flag indicating the rendering type.
	 */
	private function generateOutput($mode)
	{
		switch($mode)
		{
			case self::RENDER_XML:
				$output = $this->saveXml();
				break;

			case self::RENDER_HTML:
				$output = $this->transformToXML($this->dom);
				break;
		}

		return $output;
	}
}
