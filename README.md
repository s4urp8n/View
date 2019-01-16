# Zver\View [![Build Status](https://travis-ci.org/s4urp8n/view.svg?branch=master)](https://travis-ci.org/s4urp8n/view)

Template engine with auto escaping html entities and with full PHP-native code support

## Install

```
composer require zver/view
```

## Examples 

#### Loading from string

```php
  $html=View::loadFromString('{{ $caption}}<?=$caption2?>   <?=$caption3?>')
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render();
```
#### Loading from file
```php
  $html=View::loadFromFile($filename)
                ->set('caption', 1)
                ->render();
```
#### Autodetect loading (file or string)
```php
  $html=View::load($filenameOrString)
                ->set('caption', 1)
                ->render();
```

#### Views directories

You can add directories to search views files, instead of passing absolute filename as argument

**Last added dir is first to search**

Absolute paths also works anyway

####
```php
   View::addViewsDirectory(dir1);
   View::addViewsDirectory(dir2);
   View::addViewsDirectory(dir3); <--- added last
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
 View::load('view1');          <---- dir3/view1 because it's added last
 View::load('dir4/view1');     <---- dir4/view1
```

#### Escaping HTML entities

You can use escaping syntax in views
```
<div>
    <?=$string1?>
</div>
<div>
    {{$string2}}
</div>
```

$string1 - will rendered as it is,

$string2 - will escape HTML entities,

#### Nested usage

```
<div>
    <?=View::load($filename1)->render()?>
</div>
<div>
    <?=View::load($filename2)->render()?>
</div>
<div>
    {{$string2}}
</div>
```