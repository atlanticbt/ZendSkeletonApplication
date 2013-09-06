<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

class BaseTest extends PHPUnit_Framework_TestCase
{

	/**
	 *
	 * @var \Application\Entity\Base
	 */
	private $_sut;

	protected function setUp()
	{
		$this->_sut = $this->getMockForAbstractClass('Application\Entity\Base');
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function baseEntityShouldNotReportIsNull()
	{
		$this->assertFalse($this->_sut->isNull());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function flattenShouldReturnArray()
	{
		$this->assertInternalType('array', $this->_sut->flatten());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function extendedFlattenShouldReturnArray()
	{
		$this->assertInternalType('array', $this->_sut->flatten(true));
	}

}
