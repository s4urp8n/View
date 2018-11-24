<?php

use Zver\View;

class ViewTest extends PHPUnit\Framework\TestCase
{
    use \Zver\Package\Helper;

    public static $ext = 'php';

    public static $testFiles = [
        'view1' => '<h1><?= $caption ?></h1>',
        'view2' => '<h1><?= $caption ?></h1><h2><?= $caption2 ?></h2>',
        'view3' => '<?= $caption ?>{{ $caption2 }}{{ $caption3 }}{{mb_strtoupper($caption)}}',
        'view4' => '<h1>h1</h1>',
        'view5' => '',
        'view6' => '<?php ?>',
        'view7' => '<?php class SuperNewClass243{}',
    ];

    public function testSearchDirs()
    {
        //empty by default
        $dirs = View::getSearchDirectories();
        $this->assertSame([], $dirs);

        $dir = __DIR__ . DIRECTORY_SEPARATOR;
        //up dir
        $upDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        //loaddir
        $loadDir = $upDir . 'load' . DIRECTORY_SEPARATOR;

        //current dir
        View::addSearchDirectory($dir);

        $dirs = View::getSearchDirectories();
        $this->assertSame([$dir], $dirs);

        //current dir again
        View::addSearchDirectory($dir);
        $dirs = View::getSearchDirectories();
        $this->assertSame([$dir], $dirs);

        View::addSearchDirectory($upDir);
        $dirs = View::getSearchDirectories();
        $this->assertSame([$upDir, $dir], $dirs);

        View::addSearchDirectory($loadDir);
        $dirs = View::getSearchDirectories();
        $this->assertSame([$loadDir, $upDir, $dir], $dirs);

        View::addSearchDirectory($loadDir);
        View::addSearchDirectory($upDir);
        $dirs = View::getSearchDirectories();
        $this->assertSame([$loadDir, $upDir, $dir], $dirs);
    }

    public function testUnExistedDir()
    {
        $this->expectException('\Zver\Exceptions\View\ViewDirectoryNotFoundException');
        View::addSearchDirectory('unexistedFile');
    }

    public function testAutodetection()
    {

        $upDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;

        $this->testSearchDirs();

        //String
        $view = View::load('this is string')
                    ->render();

        $this->assertSame($view, 'this is string');

        //paths

        //view 1
        $view = View::load('view1')
                    ->set('number', 55)
                    ->render();

        $this->assertSame($view, 'view1 55');

        //view 1
        $view = View::load('view1.php')
                    ->set('number', 55)
                    ->render();

        $this->assertSame($view, 'view1 55');


        //view 2
        $view = View::load('yeap/view2')
                    ->set('number', 555)
                    ->render();

        $this->assertSame($view, 'view2 555');

        //view 2
        $view = View::load('////////\\\\\\yeap/\\\////view2')
                    ->set('number', 555)
                    ->render();

        $this->assertSame($view, 'view2 555');

        //view 2
        $view = View::load('////////\\\\\\yeap/\\\////view2.php')
                    ->set('number', 666)
                    ->render();

        $this->assertSame($view, 'view2 666');

        //absolute path
        $view = View::load($upDir . 'load////\\\view1.php')
                    ->set('number', 666)
                    ->render();

        $this->assertSame($view, 'view1 666');

        //absolute path
        $view = View::load($upDir . 'load////\\\view1')
                    ->set('number', 666)
                    ->render();

        $this->assertSame($view, 'view1 666');


    }

    public static function setUpBeforeClass()
    {
        foreach (static::$testFiles as $file => $content) {
            touch(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext);
            file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext, $content, LOCK_EX);
        }
    }

    public static function tearDownAfterClass()
    {
        foreach (static::$testFiles as $file => $content) {
            @unlink(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext);
        }
    }

    public function testRender()
    {
        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                        ->render();
        $this->assertEquals('<h1>h1</h1>', $rendered);

    }

    public function _getViewFileName($number = 1)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'view' . $number . '.php';
    }

    public function testMultipleRender()
    {
        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                        ->render();
        $this->assertEquals('<h1>h1</h1>', $rendered);

        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h2'])
                        ->render();
        $this->assertEquals('<h1>h2</h1>', $rendered);
    }

    public function testOverwriteDataAndSetDataRender()
    {
        $view = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);

        $this->assertEquals('<h1>h1</h1>', $view->render());

        $this->assertEquals(
            '<h1>h2</h1>', $view->set('caption', 'h2')
                                ->render()
        );

        $this->assertEquals(
            '<h1>h3</h1>', $view->set('caption', 'h2')
                                ->set('caption', 'h2')
                                ->set('caption', 'h4')
                                ->set('caption', 'h3')
                                ->render()
        );
    }

    public function testGet()
    {
        $view = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);
        $this->assertEquals('h1', $view->get('caption'));
    }

    public function testGetUnsetData()
    {
        $this->assertEquals(
            View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                ->get('some unset key'), null
        );
    }

    public function testToString()
    {
        $this->assertEquals('<h1>h1</h1>', View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']));
        ob_start();
        echo View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);
        $output = ob_get_clean();
        $this->assertEquals('<h1>h1</h1>', $output);
    }

    public function testLoad()
    {
        $this->assertEquals('<h1>h1</h1>', View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']));
    }

    public function testUnExistedFile()
    {
        $this->expectException('\Zver\Exceptions\View\ViewNotFoundException');
        View::loadFromFile('unexistedFile')
            ->render();
    }

    public function testMultipleSameValues()
    {
        $this->assertEquals(
            '<h1>h1</h1><h2>h2</h2>', View::loadFromFile(
            $this->_getViewFileName(2), [
                                          'caption'  => 'h1',
                                          'caption2' => 'h2',
                                      ]
        )
        );

        $this->assertEquals(
            '<h1>h1</h1><h2>h2</h2>', View::loadFromFile($this->_getViewFileName(2))
                                          ->set('caption', 'h1')
                                          ->set('caption2', 'h2')
        );

        $this->assertEquals(
            '<h1>h1</h1><h2>h1</h2>', View::loadFromFile(
            $this->_getViewFileName(2), [
                                          'caption|caption2' => 'h1',
                                      ]
        )
        );

        $this->assertEquals(
            '<h1>h1</h1><h2>h1</h2>', View::loadFromFile($this->_getViewFileName(2))
                                          ->set('caption|caption2', 'h1')
        );
    }

    public function testMagic()
    {
        $view = View::loadFromFile($this->_getViewFileName(2));
        $view->caption = 'h1';
        $view->caption2 = 'h2';

        $this->assertEquals('<h1>h1</h1><h2>h2</h2>', $view->render());
        $this->assertEquals('h1', $view->caption);
        $this->assertEquals('h2', $view->caption2);
    }

    public function testProcessingVariables()
    {
        $this->assertEquals(
            View::loadFromFile($this->_getViewFileName(3))
                ->set('caption', 1)
                ->set('caption2', 2)
                ->set('caption3', 3)
                ->render(), '1231'
        );

        $this->assertEquals(
            View::loadFromString('{{ $caption}}{{md5($caption) }}{{ md5("123") }}')
                ->set('caption', 1)
                ->render(), '1c4ca4238a0b923820dcc509a6f75849b202cb962ac59075b964b07152d234b70'
        );

    }

    public function testLoadFromString()
    {
        $this->assertEquals(
            View::loadFromString('{{ $caption}}<?=$caption2?>   <?=$caption3?>')
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render(), '1hello   meet'
        );

        $this->assertEquals(
            View::loadFromString(
                '{{ $caption}}<?=$caption2?>   <?=$caption3?>', [
                                                                  'caption'  => 1,
                                                                  'caption2' => 'hello',
                                                                  'caption3' => 'meet',
                                                              ]
            )
                ->render(), '1hello   meet'
        );
    }

    public function testLoadNoPHP()
    {
        $this->assertEquals(
            View::loadFromFile($this->_getViewFileName(4))
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render(), '<h1>h1</h1>'
        );

        $this->assertEquals(
            View::loadFromString('<h1>h1</h1>')
                ->render(), '<h1>h1</h1>'
        );
    }

    public function testSpecialFilesContent()
    {
        View::loadFromFile($this->_getViewFileName(5))
            ->render();

        View::loadFromFile($this->_getViewFileName(6))
            ->render();

        View::loadFromFile($this->_getViewFileName(7))
            ->render();

        $this->assertTrue(true);

    }

    public function testGetAllDataAndResetData()
    {
        for ($i = 1; $i <= 7; $i++) {
            $view = View::loadFromFile($this->_getViewFileName(7));

            $this->assertEquals($view->getAllData(), []);

            $view->set('key', 'value');
            $this->assertEquals($view->getAllData(), ['key' => 'value']);

            $view->set('key2', 'value2');
            $this->assertEquals(
                $view->getAllData(), [
                                       'key'  => 'value',
                                       'key2' => 'value2',
                                   ]
            );

            $view->set('key2', 'value3');
            $this->assertEquals(
                $view->getAllData(), [
                                       'key'  => 'value',
                                       'key2' => 'value3',
                                   ]
            );

            $view->resetData();
            $this->assertEquals($view->getAllData(), []);
        }
    }

}
