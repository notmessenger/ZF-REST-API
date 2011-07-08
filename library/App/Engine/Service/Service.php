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
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

namespace App\Engine\Service;

use Doctrine\ORM,
    App\System\Entity,
    App\Engine\Error;

/**
 * Base Service Class
 *
 * @since      1.0
 * @category   App
 * @package    Engine
 * @subpackage Service
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */
class Service
{
	/**
	 * Entity Manager
	 *
	 * @var ORM\EntityManager
	 */
	protected $_entityManager;

	/**
	 * Holds instance of actual Entity being serviced
	 * 
	 * @var object App\System\Entity\*
	 */
	protected $_classInstance;

	/**
	 * Get instance of Entity class extending class is representing
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// Build class instance
		preg_match('/App\\\System\\\Service\\\(.*)Service/i', get_class($this), $matches);
		$this->_classInstance = "App\System\Entity\\" . $matches[1];
	}

	/**
	 * Check that the Entity passed is the required instance 
	 * 
	 * @param $object
	 * @param $instance App\System\Entity\*
	 * @throw App\Engine\Exception
	 * @return true
	 */
	protected function _checkInstanceOf($object, $instance)
	{
		if ( ! $object instanceof $instance )
		{
			throw new \App\Engine\Exception('Object is of wrong class type', 500);
		}

		return true;
	}

	/**
	 * Get the entity manager
	 *
	 * @return ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->_entityManager;
	}

	/**
	 * Set the entity manager
	 *
	 * @param  ORM\EntityManager $entityManager
	 * @return *Service
	 */
	public function setEntityManager(ORM\EntityManager $entityManager)
	{
		$this->_entityManager = $entityManager;
		return $this;
	}

	/**
	 * Get entity by id
	 * 
	 * @param  integer $id
	 * @return mixed null|Entity\*
	 */
	public function getById($id)
	{
		/* @var $repository App\System\Repository\*Repository */
		$repository = $this->getEntityManager()
							->getRepository($this->_classInstance);

		$entities = $repository->findById($id);

		if ( 0 == $entities->count() )
		{
			return NULL;
		}
		else
		{
			foreach($entities as $entity)
			{
				return $entity;
			}
		}
	}

	/**
	 * Add single new entity
	 * 
	 * @param  Entity\* $entity
	 * @return *Service
	 */
	public function addSingle($entity)
	{
		$this->_checkInstanceOf($entity, $this->_classInstance);

		$entities[] = $entity;
		return $this->addMultiple($entities);
	}

	/**
	 * Add multiple new entities
	 * 
	 * @param  array $entity
	 * @return int Last insert Id
	 */
	public function addMultiple(array $entities)
	{
		// Check each entry for correct class instance
		foreach($entities as $entity)
		{
			$this->_checkInstanceOf($entity, $this->_classInstance);
		}

		// Persist entities
		foreach($entities as $entity)
		{
			$this->getEntityManager()->persist($entity);
		}

		$this->getEntityManager()->flush();

		return $entity->getId();
	}

	/**
	 * Reflect on the supplied object, call all 'getters' and populate distilled data representation
	 * 
	 * @param $object
	 * @return array
	 */
	public function distillAsArray($object)
	{
		// Not sure if this is a better approach given working with Entities
		// http://neal-anders.com/blog/archives/286
	
		$ref = new \ReflectionClass($object);
		$data = array();
		foreach ($ref->getMethods() as $method)
		{
			if ( 0 === strpos($method->name, 'get') && $method->isPublic() )
			{
				$name		= substr($method->name, 3);
				$name[0]	= strtolower($name[0]);
				$value		= $method->invoke($object);

				if ( 'object' === gettype($value) )
				{
					$selfMethodName = __FUNCTION__;
					$value = $this->$selfMethodName($value);
				}

				$data[$name] = $value;
			}
		}
		return $data;
	}

	/**
	 * Reflect on the supplied object, call all 'getters' and populate distilled data representation
	 * 
	 * @param $object
	 * @return object
	 */
	public function distillAsObject($object)
	{
		return (object) $this->distillAsArray($object);
	}
}
