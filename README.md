# Zver\View

Template engine with auto escaping html entities and with full PHP-native code support

## Examples

```php
 View::loadFromString('{{ $caption}}<?=$caption2?>   <?=$caption3?>')
                ->set('caption', 1)
                ->set('caption2', 'hello')
                ->set('caption3', 'meet')
                ->render();
```
OR
```php
  View::loadFromFile('filename)
                ->set('caption', 1)
                ->render();
```