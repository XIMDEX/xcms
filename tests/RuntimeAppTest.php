<?php

use Ximdex\Runtime\App;


class RuntimeAppTest extends PHPUnit_Framework_TestCase
{

    public function testApp()
    {
        $app = new App();
        $this->assertInstanceOf('Ximdex\Runtime\App', $app);
        return $app;
    }


    /**
     * @depends testApp
     */
    public function testExceptionUnableToGetMoreThanOneInstance(App $app)
    {
        $this->setExpectedException('\Exception');
        $app2 = New App();


    }

    /**
     * @depends testApp
     */
    public function testStartWithEmptyConfig(App $app)
    {

        $this->assertCount(0, $app->config());
        return $app;
    }

    /**
     * @depends testStartWithEmptyConfig
     */
    public function testAddConfigValue(App $app)
    {

        $app->setValue('key', 'value');
        $this->assertEquals($app->getValue('key'), 'value');
        return $app;
    }

    /**
     * @depends testAddConfigValue
     */

    public function testGetDefaultConfigValue(App $app)
    {
        $app->setValue('key', 'value');
        $this->assertEquals($app->getValue('xxxxxx', 'default'), 'default');
        return $app;
    }

    /**
     * @depends testGetDefaultConfigValue
     *
     */
    public function testGetNullConnectionException(App $app)
    {
        $this->setExpectedException('Exception', '-1');
        App::Db();
        return $app;
    }


    /**
     * @depends testAddConfigValue
     *
     */
    public function testSingletonApp(App $app)
    {
        $this->assertEquals($app, App::getInstance());
        return $app;
    }

    public function testAddDefaultDbConnection()
    {
        $stub = $this->getMockBuilder('PDOMock')
            ->getMock();

        App::getInstance()->addDbConnection($stub);


        $this->assertEquals($stub, App::Db());



    }

    public function testExceptionNoDbConfig()
    {
        $this->setExpectedException('Exception', '-1');

        App::Db('non-exist');


    }
    public function testSetValuePersistent()
    {

        $stub = $this->getMock('PDOMock' , array( 'prepare'));
        $stubStm = $this->getMock('PDOPreparedStatement' , array( 'execute'));

        $stub->expects($this->at(0))
            ->method('prepare')
            ->with($this->equalTo('delete from Config  where ConfigKey = :key'))
            ->will($this->returnValue( $stubStm ) );


        $stubStm->expects($this->at(0))
            ->method('execute')
            ->with($this->equalTo( array( 'key' => 'key')));

        $stub->expects( $this->at(1) )
            ->method('prepare')
            ->with($this->equalTo('insert into Config (ConfigValue, ConfigKey ) values ( :value ,:key )'))
            ->will( $this->returnValue( $stubStm ) );

        $stubStm->expects($this->at(1))
            ->method('execute')
            ->with($this->equalTo( array( 'key' => 'key', 'value' => 'value')));


        App::getInstance()->addDbConnection($stub);



        $this->assertEquals($stub, App::Db());
        App::setValue( 'key', 'value', true ) ;

    }
}

class PDOMock extends \PDO
{
    public function __construct()
    {
    }


}
