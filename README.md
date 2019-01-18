# vcn/symfony-autofactory

vcn/symfony-autofactory makes it easy to define service factory classes for Symfony.

# Usage

Please make sure you have a good understanding of how dependency injection works in Symfony. You can find their documentation [here](https://symfony.com/doc/current/service_container.html).

To start using vcn/symfony-autofactory, the easiest approach is to install the vcn/symfony-autofactory-bundle. If you ensure all AutoFactory-instances are autoconfigured, the rest will work automatically.

If you do not want to use the bundle, you need to:
- add the AutoFactoryPass to your kernel compiler passes
- ensure that your AutoFactory-instances all have the tag you configured in the AutoFactoryPass

# Usage

## Basic usage

To create an AutoFactory, create a class that implements the AutoFactory interface. For a class method to be considered a factory it MUST be public, it MUST be static, and it MUST define a class return type.

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;

class FooFactory implements AutoFactory
{
    public static function createFoo(): Foo
    {
        return new Foo();
    }
}
``` 

## Configuration
The factories can be fine tuned using annotations.

### @​Alias

The concept of aliases is demonstrated [here](https://symfony.com/doc/current/service_container.html#explicitly-configuring-services-and-arguments) in the Symfony documentation. You can add one or more aliases to your dependency by adding one or more `@Alias`-annotations to your factory method. The annotation MUST receive two named arguments: `id: string` and `public: bool`. The names are self-explanatory.

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Alias;

class FooFactory implements AutoFactory
{
    /**
     * @Alias(id="foo.service", public=true)
     */
    public static function createFoo(): Foo
    {
        return new Foo();
    }
}
```

### @​Autoconfigure

The concept of autoconfiguration is explained [here](https://symfony.com/doc/current/service_container.html#the-autoconfigure-option) in the Symfony documentation. By default, factories are autoconfigured. You can change this at class-level and at method-level with the `@Autoconfigure`-annotation. The annotation takes one unnamed boolean parameter.
 
```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Autoconfigure;

/**
 * Override the default, now all factory methods in this class are not autoconfigured by default
* @Autoconfigure(false)
 */
class FooFactory implements AutoFactory
{
    /**
     * But we want autoconfiguration for this specific dependency
     * @Autoconfigure(true)
     */
    public static function createFoo(): Foo
    {
        return new Foo();
    }
}
```

### @​Autowire

The concept of autowiring is explained [here](https://symfony.com/doc/current/service_container/autowiring.html) and [here](https://symfony.com/doc/current/service_container.html#the-autowire-option) in the Symfony documentation. By default, factories are autowired. You can change this at class-level and at method-level with the `@Autowire`-annotation. The annotation takes one unnamed boolean parameter.
 
```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Autowire;

/**
 * Override the default, now all factory methods in this class are not autowired by default
 * @Autowire(false)
 */
class FooFactory implements AutoFactory
{
    public static function createFoo(): Foo
    {
        return new Foo();
    }
    
    /**
     * But we want autowiring for this specific method
     * @Autowire(true)
     */
    public static function createBar(): Bar
    {
        return new Bar();
    }
}
```

### @​Bind

The concept of binding arguments is explained [here](https://symfony.com/doc/current/service_container.html#binding-arguments-by-name-or-type) in the Symfony documentation. To bind parameters of a factory method to a dependency specified by id, you can use the `@Bind`-annotation. The annotation MUST receive two named arguments: `arg: string` and `id: string`. The value for `arg` refers to the name of the argument of the factory method being bound, and should include the leading dollar-sign. The `id` should refer to a valid service id.  

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Bind;

class FooFactory implements AutoFactory
{
    /**
     * @Bind(arg="$barService", id="bar.service")
     */
    public static function createFoo(Bar $barService): Foo
    {
        return new Foo($barService->getSomethingINeed());
    }
}
```

### @​Id

The concept of ids is hinted at [here](https://symfony.com/doc/current/service_container.html#choose-a-specific-service) in the Symfony documentation. When no `@Id`-annotation is used, the fully-qualified class name of the dependency is used as id. You can override this with the `@Id`-annotation. The annotation MUST receive one unnamed string argument, containing the id to set. Having two or more `@Id`-annotations is not possible. For the use cases where a dependency should be adressable with multiple ids, please use the `@Alias`-annotation.

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Bind;

class FooFactory implements AutoFactory
{
    /**
     * @Id("foo.consumer")
     */
    public static function createConsumerFoo(): Foo
    {
        return new Foo();
    }
    
    /**
     * @Id("foo.business")
     */
    public static function createBusinessFoo(): Foo
    {
        return new Foo();
    }
    
    /**
     * @Bind(arg="$foo", id="foo.consumer")
     */
    public static function createConsumerBar(Foo $foo): Bar
    {
        return new Bar($foo);
    }
    
    /**
     * @Bind(arg="$foo", id="foo.business")
     */
    public static function createBusinessBar(Foo $foo): Bar
    {
        return new Bar($foo);
    }
}
```

### @​IsPublic
The concept of publicness is explained [here](https://symfony.com/doc/current/service_container.html#public-versus-private-services) in the Symfony documentation. By default, factories are not public. You can change this at class-level and at method-level with the `@IsPublic`-annotation. The annotation MUST received one unnamed boolean argument.

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;

/**
 * Override the default, now all factory methods in this class are public by default
* @IsPublic(true)
 */
class FooFactory implements AutoFactory
{
    public static function createFoo(): Foo
    {
        return new Foo();
    }
    
    /**
     * We can override again. This dependency will not be public after all. 
     * @IsPublic(false)
     */
    public static function createBar(): Bar
    {
        return new Bar();
    }
}
```

### @​Tag

The concept of tags is explained [here](https://symfony.com/doc/current/service_container/tags.html) in the Symfony documentation. You can add one or more `@Tag`-annotations to any factory method. Every `@Tag`-annotation MUST have one unnamed string argument defining the name, and MAY have more named arguments defining additional tag attributes.

```php
<?php

use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\Annotation\Tag;

class FooFactory implements AutoFactory
{
    /**
     * @Tag("some.tag")
     * @Tag("foo.tag", important=true, bar="baz")
     */
    public static function createFoo(): Foo
    {
        return new Foo();
    }
}
```

## Examples

Examples can be found in the examples-directory.
 


