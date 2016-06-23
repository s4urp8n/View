# Zver\View

## Table of Contents

* [View](#view)
    * [loadFromFile](#loadfromfile)
    * [set](#set)
    * [loadFromString](#loadfromstring)
    * [__toString](#__tostring)
    * [render](#render)
    * [__get](#__get)
    * [__set](#__set)
    * [get](#get)
    * [resetData](#resetdata)
    * [getAllData](#getalldata)
* [ViewNotFoundException](#viewnotfoundexception)
    * [__construct](#__construct)

## View

Template engine with auto escaping html entities and with full PHP-native code support



* Full name: \Zver\View


### loadFromFile

Create view instance, set content and data from second argument

```php
View::loadFromFile( string $file, array $data = array() ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$file` | **string** |  |
| `$data` | **array** |  |




---

### set

Store value associated with key in data array

```php
View::set( string $key, mixed $value ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string** |  |
| `$value` | **mixed** |  |




---

### loadFromString

Create instance, set view content from string and data from second argument

```php
View::loadFromString(  $string, array $data = array() ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | **** |  |
| `$data` | **array** |  |




---

### __toString

Auto render view to string if needed

```php
View::__toString(  ): string
```







---

### render

Extract data into variable and get string representation of view file

```php
View::render(  ): string
```







---

### __get

Magic method equals to get() method

```php
View::__get(  $key ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **** |  |




---

### __set

Magic method equals to set() method

```php
View::__set(  $key,  $value ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **** |  |
| `$value` | **** |  |




---

### get

Get value from data array. If value is not set throws ViewDataNotFoundException.

```php
View::get( string $key ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string** |  |




---

### resetData

Clear all data associated with current view

```php
View::resetData(  ): $this
```







---

### getAllData

Get all data associated with current view

```php
View::getAllData(  ): array
```







---

## ViewNotFoundException





* Full name: \Zver\Exceptions\View\ViewNotFoundException
* Parent class: 


### __construct

Override parent constructor to see custom exception message

```php
ViewNotFoundException::__construct(  $filePath )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filePath` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2016-06-23 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
