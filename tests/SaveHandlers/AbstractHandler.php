<?php namespace Tests\Session\SaveHandlers;

use Tests\Session\SessionTest;

/**
 * Class AbstractHandler.
 *
 * @runTestsInSeparateProcesses
 */
class AbstractHandler extends SessionTest
{
	public function setUp() : void
	{
		$_SERVER['REMOTE_ADDR'] = '192.168.0.100';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:88.0) Gecko/20100101 Firefox/88.0';
		parent::setUp();
	}

	public function testValidateId()
	{
		$id6 = '62my7tSXcbIrOZ-WHsEXhpwUoG,afmBQNGaSBkFN';
		$id5 = 'iimuf8lvdectatt5jtkve15831funl8rg5cg6okp';
		$id4 = '96aa2c863140e0e714a603cf44b0afc9a0632592';
		$this->session->stop();
		\ini_set('session.sid_bits_per_character', 6);
		\ini_set('session.sid_length', 40);
		$this->assertTrue($this->handler->validateId($id6));
		$this->assertTrue($this->handler->validateId($id5));
		$this->assertTrue($this->handler->validateId($id4));
		\ini_set('session.sid_bits_per_character', 5);
		$this->assertFalse($this->handler->validateId($id6));
		$this->assertTrue($this->handler->validateId($id5));
		$this->assertTrue($this->handler->validateId($id4));
		\ini_set('session.sid_bits_per_character', 4);
		$this->assertFalse($this->handler->validateId($id6));
		$this->assertFalse($this->handler->validateId($id5));
		$this->assertTrue($this->handler->validateId($id4));
	}

	public function testGC()
	{
		$this->session->stop();
		$this->session->start([
			'cookie_lifetime' => 1,
		]);
		$this->session->foo = 'bar';
		$this->assertEquals($this->session->foo, 'bar');
		$this->session->stop();
		\sleep(1);
		$this->assertTrue($this->handler->gc(0));
		$this->session->start();
		$this->assertNull($this->session->foo);
	}

	public function testIP()
	{
		$this->assertEquals('192.168.0.100', $this->handler->getIP());
	}

	public function testUA()
	{
		$this->assertEquals(
			'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:88.0) Gecko/20100101 Firefox/88.0',
			$this->handler->getUA()
		);
	}
}
