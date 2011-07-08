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
 * @package    Api
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

use App\Engine,
	App\System\Entity\Country;

/**
 * Country controller
 *
 * @since      1.0
 * @category   Application
 * @package    Api
 * @subpackage Controller
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class Api_CountryController
	extends App_Engine_Controller_Resource
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
		'OPTIONS',
		'HEAD',
	);

	/**
	 * Get representation of existing resource
	 */
	public function getAction()
	{
		$countryId = (int) $this->_getParam('countryId');

		if ( 0 == $countryId )
		{
			throw new \App\Engine\Exception('Country Id is empty or not an integer.', 400);
		}

		/* @var $countryService App\System\Service\CountryService*/
		$countryService = Zend_Registry::get('di')->get('service.country');

		$country = $countryService->getById($countryId);

		if ( NULL === $country )
		{
			throw new \App\Engine\Exception('No Country found by Id provided.', 404);
		}
		else
		{
			// Send the data to the view for appropriate formatting
			$this->view->data = $countryService->distillAsObject($country);
		}
	}

	/**
	 * Create new resource
	 */
	public function postAction()
	{
		$data = $this->_request->getPost();

		if ( ! is_array($data) || empty($data) )
		{
			throw new \App\Engine\Exception('No usable data provided in request.', 400);
		}

		/* @var $countryService App\System\Service\CountryService*/
		$countryService = Zend_Registry::get('di')->get('service.country');

		$resourceIds = array();

		$country = new Country();
		$country->setName($data['country']['name']);
		$country->setRegionCode($data['country']['regionCode']);
		$country->setCallingPrefix($data['country']['callingPrefix']);
		$country->setCallingCode($data['country']['callingCode']);

		$this->view->data		= $countryService->addSingle($country);
		$this->view->service	= $countryService;
	}

	/**
	 * Update existing resource
	 */
	public function putAction()
	{
		// Data Payload
		$data = $this->_helper->Params();

		if ( ! is_array($data) || empty($data) )
		{
			throw new \App\Engine\Exception('No data provided in request.', 400);
		}

		// Country Id
		$countryId = $this->_getParam('countryId');

		if ( ! $countryId )
		{
			throw new \App\Engine\Exception('No Country Id provided.', 400);
		}

		/* @var $countryService App\System\Service\CountryService*/
		$countryService = Zend_Registry::get('di')->get('service.country');

		$country = $countryService->getById($countryId);

		if ( NULL === $country )
		{
			throw new \App\Engine\Exception('No Country found by Id provided.', 404);
		}
		else
		{
			$country->setName($data['country']['name']);
			$country->setCallingPrefix($data['country']['callingPrefix']);
			$countryService->update($country);

			$this->view->data		= $countryId;
			$this->view->service	= $countryService;
		}
	}

	/**
	 * Delete existing resource
	 */
	public function deleteAction()
	{
		/* @var $countryService App\System\Service\CountryService*/
		$countryService = Zend_Registry::get('di')->get('service.country');

		$country = $countryService->getById($this->_getParam('countryId'));

		if ( null !== $country )
		{
			$countryService->remove($country);
		}
	}

	/**
	 * Return supported Options
	 */
	public function optionsAction()
	{
		$this->sendOptionsReply();
	}

	/**
	 * Return response headers
	 */
	public function headAction()
	{
		$this->sendHeadReply();
	}
}
