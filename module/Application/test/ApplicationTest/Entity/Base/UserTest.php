<?php

namespace ApplicationTest\Entity\Base;

use Application\Entity\Base\UserInterface;
use PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase
{

	/**
	 *
	 * @var \Application\Entity\Base\User
	 */
	private $_sut;

	protected function setUp()
	{
		$this->_sut = new \Application\Entity\Base\User();
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function setIdShoulNotChangeIdValue()
	{

	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function usernameStartsNull()
	{
		$this->assertNull($this->_sut->getUsername());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function usernameGetterAndSetterWork()
	{
		$username = 'Test Value';
		$this->_sut->setUsername($username);
		$this->assertEquals($username, $this->_sut->getUsername());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function emailStartsNull()
	{
		$this->assertNull($this->_sut->getEmail());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function emailGetterAndSetterWork()
	{
		$email = 'Test Value';
		$this->_sut->setEmail($email);
		$this->assertEquals($email, $this->_sut->getEmail());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function diaplyNameStartsNull()
	{
		$this->assertNull($this->_sut->getDisplayName());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function displayNameGetterAndSetterWork()
	{
		$displayName = 'Test Value';
		$this->_sut->setDisplayName($displayName);
		$this->assertEquals($displayName, $this->_sut->getDisplayName());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function passwordStartsNull()
	{
		$this->assertNull($this->_sut->getPassword());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function passwordGetterAndSetterWork()
	{
		$password = 'some password';
		$this->_sut->setPassword($password);
		$this->assertEquals($password, $this->_sut->getPassword());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function stateStartsInactive()
	{
		$this->assertEquals(UserInterface::STATE_INACTIVE, $this->_sut->getState());
	}

	/**
	 * @test
	 * @group entity
	 * @group jfoushee
	 */
	public function stateGetterAndSetterWork()
	{
		$state = 'newstate';
		$this->_sut->setState($state);
		$this->assertEquals($state, $this->_sut->getState());
	}

}
