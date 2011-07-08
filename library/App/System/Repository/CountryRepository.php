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
 * @subpackage Repository
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

namespace App\System\Repository;

use Doctrine\ORM,
	DoctrineExtensions\Paginate,
    App\System\Country;

/**
 * Prefix Repository
 *
 * @since      1.0
 * @category   App
 * @package    System
 * @subpackage Repository
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class CountryRepository extends ORM\EntityRepository
{
    /**
     * Find by 'Id'
     *
     * @param integer $id
     * @return Zend_Paginator
     */
	public function findById($id)
	{
		$query = $this->getEntityManager()->createQueryBuilder()
					->select('p')
					->from('App\System\Entity\Country', 'p')
					->where('p.id = :identifier')
					->setParameter('identifier', $id)
					->getQuery();

		return new \Zend_Paginator(new Paginate\PaginationAdapter($query));
	}

	/**
	 * Find all with no conditions
	 * 
	 * @return object Zend_Paginator
	 */
	public function findAll()
	{
		$query = $this->getEntityManager()->createQueryBuilder()
					->select('c')
					->from('App\System\Entity\Country', 'c')
					->getQuery();

		return new \Zend_Paginator(new Paginate\PaginationAdapter($query));
	}
}
