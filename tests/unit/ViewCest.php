<?php

use Zver\View;

class ViewCest
{
    
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
    
    public function _before()
    {
        $srcDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        
        foreach (static::$testFiles as $file => $content)
        {
            touch(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext);
            file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext, $content, LOCK_EX);
        }
        
    }
    
    public function _after()
    {
        foreach (static::$testFiles as $file => $content)
        {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . $file . '.' . static::$ext);
        }
    }
    
    public function testRender(UnitTester $I)
    {
        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                        ->render();
        $I->assertEquals('<h1>h1</h1>', $rendered);
        
    }
    
    public function _getViewFileName($number = 1)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'view' . $number . '.php';
    }
    
    public function testMultipleRender(UnitTester $I)
    {
        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                        ->render();
        $I->assertEquals('<h1>h1</h1>', $rendered);
        
        $rendered = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h2'])
                        ->render();
        $I->assertEquals('<h1>h2</h1>', $rendered);
    }
    
    public function testOverwriteDataAndSetDataRender(UnitTester $I)
    {
        $view = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);
        
        $I->assertEquals('<h1>h1</h1>', $view->render());
        
        $I->assertEquals(
            '<h1>h2</h1>', $view->set('caption', 'h2')
                                ->render()
        );
        
        $I->assertEquals(
            '<h1>h3</h1>', $view->set('caption', 'h2')
                                ->set('caption', 'h2')
                                ->set('caption', 'h4')
                                ->set('caption', 'h3')
                                ->render()
        );
    }
    
    public function testGet(UnitTester $I)
    {
        $view = View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);
        $I->assertEquals('h1', $view->get('caption'));
    }
    
    public function testGetUnsetData(UnitTester $I)
    {
        $I->assertEquals(
            View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1'])
                ->get('some unset key'), null
        );
    }
    
    public function testToString(UnitTester $I)
    {
        $I->assertEquals('<h1>h1</h1>', View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']));
        ob_start();
        echo View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']);
        $output = ob_get_clean();
        $I->assertEquals('<h1>h1</h1>', $output);
    }
    
    public function testLoad(UnitTester $I)
    {
        $I->assertEquals('<h1>h1</h1>', View::loadFromFile($this->_getViewFileName(), ['caption' => 'h1']));
    }
    
    public function testUnExistedFile(UnitTester $I)
    {
        $I->expectException(
            '\Zver\Exceptions\View\ViewNotFoundException', function ()
        {
            
            View::loadFromFile('unexistedFile')
                ->render();
        }
        );
    }
    
    public function testMultipleSameValues(UnitTester $I)
    {
        $I->assertEquals(
            '<h1>h1</h1><h2>h2</h2>', View::loadFromFile(
            $this->_getViewFileName(2), [
                                          'caption'  => 'h1',
                                          'caption2' => 'h2',
                                      ]
        )
        );
        
        $I->assertEquals(
            '<h1>h1</h1><h2>h2</h2>', View::loadFromFile($this->_getViewFileName(2))
                                          ->set('caption', 'h1')
                                          ->set('caption2', 'h2')
        );
        
        $I->assertEquals(
            '<h1>h1</h1><h2>h1</h2>', View::loadFromFile(
            $this->_getViewFileName(2), [
                                          'caption|caption2' => 'h1',
                                      ]
        )
        );
        
        $I->assertEquals(
            '<h1>h1</h1><h2>h1</h2>', View::loadFromFile($this->_getViewFileName(2))
                                          ->set('caption|caption2', 'h1')
        );
    }
    
    public function testMagic(UnitTester $I)
    {
        $view = View::loadFromFile($this->_getViewFileName(2));
        $view->caption = 'h1';
        $view->caption2 = 'h2';
        
        $I->assertEquals('<h1>h1</h1><h2>h2</h2>', $view->render());
        $I->assertEquals('h1', $view->caption);
        $I->assertEquals('h2', $view->caption2);
    }
    
    public function testProcessingVariables(UnitTester $I)
    {
        $I->assertEquals(
            View::loadFromFile($this->_getViewFileName(3))
                ->set('caption', 1)
                ->set('caption2', 2)
                ->set('caption3', 3)
                ->render(), '1231'
        );
        
        $I->assertEquals(
            View::loadFromString('{{ $caption}}{{md5($caption) }}{{ md5("123") }}')
                ->set('caption', 1)
                ->render(), '1c4ca4238a0b923820dcc509a6f75849b202cb962ac59075b964b07152d234b70'
        );
        
    }
    
    public function testLoadFromString(UnitTester $I)
    {
        $I->assertEquals(
            View::loadFromString('{{ $caption}}<?=$caption2?>   <?=$caption3?>')
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render(), '1hello   meet'
        );
        
        $I->assertEquals(
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
    
    public function testLoadNoPHP(UnitTester $I)
    {
        $I->assertEquals(
            View::loadFromFile($this->_getViewFileName(4))
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render(), '<h1>h1</h1>'
        );
        
        $I->assertEquals(
            View::loadFromString('<h1>h1</h1>')
                ->render(), '<h1>h1</h1>'
        );
    }
    
    public function testSpecialFilesContent(UnitTester $I)
    {
        View::loadFromFile($this->_getViewFileName(5))
            ->render();
        
        View::loadFromFile($this->_getViewFileName(6))
            ->render();
        
        View::loadFromFile($this->_getViewFileName(7))
            ->render();
        
    }
    
    public function testGetAllDataAndResetData(UnitTester $I)
    {
        for ($i = 1; $i <= 7; $i++)
        {
            $view = View::loadFromFile($this->_getViewFileName(7));
            
            $I->assertEquals($view->getAllData(), []);
            
            $view->set('key', 'value');
            $I->assertEquals($view->getAllData(), ['key' => 'value']);
            
            $view->set('key2', 'value2');
            $I->assertEquals(
                $view->getAllData(), [
                                       'key'  => 'value',
                                       'key2' => 'value2',
                                   ]
            );
            
            $view->set('key2', 'value3');
            $I->assertEquals(
                $view->getAllData(), [
                                       'key'  => 'value',
                                       'key2' => 'value3',
                                   ]
            );
            
            $view->resetData();
            $I->assertEquals($view->getAllData(), []);
        }
    }
    
}
