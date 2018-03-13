# base包

> doctrine2 数据操作
> 读取配置


## 1 安装(Install)
> 1. 通过Composer安装
> 2. 创建composer.json文件,并写入以下内容:

```json
{
  "require": {
    "zwei/base": "dev-develop"
  }
}
```
> 3. 执行composer install


## 使用示例(use)
> 1. 例如项目目录在"E:\web\php7\test"
> 2. 创建index.php,并加入以下内容

```php
<?php
// 读取配置
\Zwei\Base\Config::get("配置键名");
// 返回\Doctrine\DBAL\Connection类,请查看doctrine 官网文档
$dbConnection = DB::getInstance()->getConnection();
```

### 单元测试使用
> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./Tests/TestInit.php ./Tests/

D:\phpStudy\php\php-7.1.13-nts\php.exe D:\phpStudy\php\php-5.6.27-nts\composer.phar update

D:\phpStudy\php\php-7.1.13-nts\php.exe vendor\phpunit\phpunit\phpunit --bootstrap tests/TestInit.php tests/

D:\phpStudy\php\php-7.1.13-nts\php.exe index.php