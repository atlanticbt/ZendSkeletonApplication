<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

class BaseNullTest extends PHPUnit_Framework_TestCase
{

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
		$this->assertTrue(is_array($this->_sut->flatten()));
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function extendedFlattenShouldReturnArray()
	{
		$this->assertTrue(is_array($this->_sut->flatten(true)));
	}

}
