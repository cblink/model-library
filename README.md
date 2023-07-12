<h1 align="center"> model-library </h1>

<p align="center"> .</p>


## 安装

```shell
$ composer require cblink/model-library -vvv
```

## 配置

### Laravel 配置
修改 config/app.php ，增加以下参数
```php
<?php

return [
	// ...
	
	'paginate' => [
		'all_key' => 'is_all',
		'page_key' => 'pre_page',
	]
];
```

## 使用

### 分页示例
#### 引用
给 Model 引入 Cblink\ModelLibrary\Laravel\PageableTrait 

```php
<?php

namespace App;
use Cblink\ModelLibrary\Laravel\PageableTrait;

class User extends Model 
{
	use PageableTrait;
	// ...
}
```

#### 使用示例

```php
<?php

// 每页数量字段使用配置名 'app.paginate.page_key'
User::query()->page();

// 使用简单分页（不包含页码），每页数量字段使用配置名 'app.paginate.page_key'
User::query()->simplePage();

// 如果前端传入了字段 `app.paginate.all_key`，则获取所有数据，如不传入，则输出分页数据，其他同上
User::query()->pageOrAll();

// 同上
User::query()->simplePageOrAll();
```

### 搜索示例

#### 引用
给 Model 引入 Cblink\ModelLibrary\Laravel\SearchableTrait 

```php
<?php

namespace App;
use Cblink\ModelLibrary\Laravel\SearchableTrait;

class User extends Model 
{
	use SearchableTrait;
	// ...
}
```
#### 使用

**配置结构**

```php
<?php
// 所有的搜素都必须保证两个条件才会触发
// 1. 前端有传入值，如果前端没有传入值将不会触发搜索
// 2. 填写了默认值，default字段

User::query()->search([
	// 这里的 query 为前端传入的参数名将使用参数值进行搜索匹配
	'query' => [
		"filed" => "匹配数据库的字段名",
		"type" => "搜索类型",
		"mix" => "混合条件",
		"group" => "搜索分组",
		"default" => "默认值",
		"relate" => "关联查询",
	]
])->get();
```

**搜索类型(type )**

- eq (默认)
```
User::search([
	'status' => []
]);

// 等价搜索
User::when($request->input('status'), function($query, $status){
	$query->where('status', $status);
});
```

- lt

```
User::search([
	'age' => ['type' => 'lt']
]);

// 等价搜索
User::when($request->input('age'), function($query, $age){
	$query->where('age', '<', $age);
});
```

- lte
```
<=
```

- gt
```
>
```

- gte
```
>=
```

- date
```
User::search([
	'created_at' => ['type' => 'date']
]);
// 等价搜索
// 当前端传值为 2020-02-02 时
$query->whereDate('created_at', $date)

// 当前端传值为 2020-02-02 ~ 2020-02-05 时
$query->whereDate('created_at', '>=', $start_at)
		->where('created_at', '<=', $end_at)
```

- datetime

同date用法一致，无非就是这里是可以搜索具体时间，而date只能已Day单位搜索

- keyword
```
User::search([
	'name' => ['type' => 'keyword']
]);
// 等价搜索
$query->whereDate('name', 'LIKE', $name . '%');
```

- in

```
User::search([
	'status' => ['type' => 'in']
]);
// 等价搜索 , is_array部分无需后端处理，根据前端传值来处理
$query->whereIn('status', is_array($status) ? $status : [$status]);
```


**混合条件(mix )**

- and （默认）
```
//当出现多个搜索条件时，默认以 and 条件并行

User::search([
	'name' => ['type' => 'eq'],
	'age' => ['type' => 'le']

]);
// 等价搜索
User::where(['name' => $name])->where('age', '<', $age);
```

- or
```
// 如果想使用或条件，只需要增加 mix 参数即可

User::search([
	'name' => ['type' => 'eq'],
	'age' => ['type' => 'le', 'mix' => 'or']
]);

// 等价搜索
User::where(['name' => $name])->orWhere('age', '<', $age);
```

**分组搜索 (group)**
```
如果不指定此参数，所有的搜索条件都会归于 default  组中，如果需要区分搜索的组，可以使用group 参数实现
User::search([
	'name' => ['type' => 'eq', 'group' => '1'],
	'age' => ['type' => 'gt', 'group' => '2'],
	'sex' => ['type' => 'eq', 'group' => 1],
]);

// 等价搜索
User::where(function(){
	$query->where('age', '>' , $age)
		  ->orWhere('sex', $sex);
})->where('name', $name);
```

**字段搜索 (field)**
```
指定需要搜索的字段，用于参数名与数据库字段名不一致的情况使用
User::search([
	'name' => ['filed' => 'nickname'],
]);

// 等价搜索
User::where('nickname', $name);
```

**关联查询 (relate)**
```
User::search([
	'number' => ['relate' => 'order', 'field' => 'order_no'],
]);

// 等价搜索
User::whereHas('order', function($query){
	$query->where('order_no', $number);
});
```

**默认值（default）**
```
User::search([
	'name' => ['default' => '123'],
]);

// 等价搜索
User::where('name', $request->get("name", "123"));
```

**输入覆盖（value)**

优先顺序，value  > $request->get('value')  > default 
```
User::search([
	'name' => ['value' => '123'],
]);

// 等价搜索

// 即使request->get('name')存在传入值，也不会进行引用。
User::where('name', 123);
```

**复杂示例**

场景1：当要求实现一个输入框同时对3个字段进行检索。
```
// 同一个输入框需要支持多个搜索
User::search([
	'query' => [
		['type' => 'keyword', 'field' => 'name', 'group' => 1, 'mix' =>'or'],
		['type' => 'keyword', 'field' => 'desc', 'group' => 1, 'mix' =>'or'],
		['type' => 'eq', 'field' => 'id', 'group' => 1],
	],
	'status' => [’type’ => 'in']
]);

// 等价搜索
User::where(function($query){
	$query->where('id', $query)
		  ->orWhere('name', 'LIKE', $query . '%')
		  ->orWhere('desc', 'LIKE', $query . '%')
})->whereIn('status', is_array($status) ? $status : [$status]);
```


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/cblink/laravel-model-simple-search/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/cblink/laravel-model-simple-search/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
