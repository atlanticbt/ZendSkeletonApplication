<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

class BaseNullTest extends PHPUnit_Framework_TestCase
{

	/**
	 *
	 * @var \Application\Entity\BaseNull
	 */
	private $_sut;

	protected function setUp()
	{
		$this->_sut = $this->getMockForAbstractClass('Application\Entity\BaseNull');
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function entityShouldReportIsNull()
	{
		$this->assertTrue($this->_sut->isNull());
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
