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
 * @package    System
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

namespace App\System\Service;

use App\System\Entity,
    App\Engine\Service;

/**
 * Country Service
 *
 * @since      1.0
 * @category   App
 * @package    System
 * @subpackage Service
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class CountryService extends Service\Service
{
	/**
	 * Update specified Country entity
	 * 
	 * @param object \App\System\Entity\Country $entity
	 * @return void
	 */
	public function update(\App\System\Entity\Country $entity)
	{
		$this->getEntityManager()->persist($entity);
		$this->getEntityManager()->flush();
	}

	/**
	 * Remove specified Country entity
	 * 
	 * @param object \App\System\Entity\Country $entity
	 * @return void
	 */
	public function remove(\App\System\Entity\Country $entity)
	{
		$this->getEntityManager()->remove($entity);
		$this->getEntityManager()->flush();
	}

	/**
	 * Get all countries
	 * 
	 * @return object Zend_Paginator
	 */
	public function getAll()
	{
		/* @var $repository App\System\Repository\CountryRepository */
		$repository = $this->getEntityManager()
							->getRepository('App\System\Entity\Country');

		return $repository->findAll();
	}
}
