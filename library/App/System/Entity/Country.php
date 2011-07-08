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
 * @subpackage Entity
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 */

namespace App\System\Entity;

/**
 * Country
 *
 * @since      1.0
 * @category   App
 * @package    System
 * @subpackage Entity
 * @author     Jeremy Brown <jeremy@notmessenger.com>
 * @copyright  Copyright (c) 2011 Jeremy Brown (http://www.notmessenger.com/)
 *
 * @Entity(
 *     repositoryClass = "App\System\Repository\CountryRepository"
 * )
 */
class Country
{
	/**
	 * Id
	 * 
	 * @var integer
	 * 
	 * @Column(
	 * 		type	= "integer"
	 * )
	 * @Id
	 * @GeneratedValue(strategy = "IDENTITY")
	 */
	private $id;

	/**
	 * Name
	 * 
	 * @var string
	 * 
	 * @Column(
	 * 		type	= "string",
	 * 		length	= 255
	 * )
	 */
	private $name;

	/**
	 * Region Code
	 * 
	 * Are the codes found at http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_territory_information.html
	 * Am using this list, as it is what Zend Framework uses as the basis for Zend_Local and other locale-aware modules.
	 * Additional information and code types can be found at http://en.wikipedia.org/wiki/Country_codes
	 * 
	 * @var string
	 * 
	 * @Column(
	 * 		type	= "string",
	 * 		length	= 2
	 * )
	 */
	private $regionCode;

	/**
	 * International Calling Prefix
	 * 
	 * http://en.wikipedia.org/wiki/List_of_international_call_prefixes
	 * 
	 * @var string
	 * 
	 * @Column(
	 * 		type		= "string",
	 * 		length		= 5
	 * )
	 */
	private $callingPrefix;

	/**
	 * Country Calling Code
	 * 
	 * http://en.wikipedia.org/wiki/List_of_country_calling_codes
	 * 
	 * @var string
	 * 
	 * @Column(
	 * 		type	= "string",
	 * 		length	= 5
	 * )
	 */
	private $callingCode;

	/**
	 * Gets the ID of the Country
	 * 
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the country name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the country name
	 * 
	 * @param string $name
	 * @return Country
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the Region Code
	 * 
	 * @return string
	 */
	public function getRegionCode()
	{
		return $this->regionCode;
	}

	/**
	 * Set the Region Code
	 * 
	 * @param string $regionCode
	 * @return Country
	 */
	public function setRegionCode($regionCode)
	{
		$this->regionCode = $regionCode;

		return $this;
	}

	/**
	 * Get the Calling Prefix
	 * 
	 * @return string
	 */
	public function getCallingPrefix()
	{
		return $this->callingPrefix;
	}

	/**
	 * Set the Calling Prefix
	 * 
	 * @param string $callingPrefix
	 * @return Country
	 */
	public function setCallingPrefix($callingPrefix)
	{
		$this->callingPrefix = $callingPrefix;

		return $this;
	}

	/**
	 * Get the Calling Code
	 * 
	 * @return string
	 */
	public function getCallingCode()
	{
		return $this->callingCode;
	}

	/**
	 * Set the Calling Code
	 * 
	 * @param string $callingCode
	 * @return Country
	 */
	public function setCallingCode($callingCode)
	{
		$this->callingCode = $callingCode;

		return $this;
	}
}
