# Zver\View

Template engine with auto escaping html entities and with full PHP-native code support

## Examples

Loading from string
```php
  View::loadFromString('{{ $caption}}<?=$caption2?>   <?=$caption3?>')
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render();
```
OR from file
```php
  View::loadFromFile($filename)
                ->set('caption', 1)
                ->render();
```
OR autodetect
```php
  View::load($filenameOrString)
                ->set('caption', 1)
                ->render();
```
Also you can add unlimited directories to search views files
Last added dir is first to search
```php
     View::addSearchDirectory(dir1);
     View::addSearchDirectory(dir2);
     View::addSearchDirectory(dir3); <--- added last
```

Example directory structure
```
\dir3
	\view1.php
\dir2
    \dir4
        \view1.php
	\view1.php
	\view2.php
\dir1
	\view1.php
	\view3.php
```

and load views like this
```php
 View::load('view3');          <---- dir1/view3
 View::load('view1');          <---- dir3/view1 because it's latestly added
 View::load('dir4/view1');     <---- dir4/view1
```
