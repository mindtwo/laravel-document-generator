# Placeholder

Inside your templates html strings you can specify **Placeholder** which will automatically be populated
with the corresponding value on generation.

## Placeholder in templates

A placeholder in an html string is specified via a simple `{placeholder_name}` or `{modelClass.path.to.modelValue}`.

e.g.

```html
<div> {test} </div>
```
will look for a placeholder called **Test** and resolve the value

```html
<div> resolved value of test </div>
```

Another example for a document attached to the `User` model would be

```html
<div> {user.name} </div>
```
which will try to resolve the value by looking up the users name

```html
<div> Username </div>
```

**Note on blade:**

Since blade has the possibility to use variables by itself the package uses this functionality. This means
that a placeholder inside blade is specified via the "normal" notation which would result for the first example
in:


```html
<div> {{ $test }} </div>
```

## Create a Placeholder

To create a placeholder simply run `php artisan make:placeholder name` which will generate a default Placeholder.

e.g. if you run `php artisan make:placeholder foo` the class `FooPlaceholder` will be generated.

```php
use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Document\Placeholder;

class FooPlaceholder extends Placeholder
{
    public function resolve(Model $model): string
    {
        // TODO resolve your value
        return '';
    }
}
```

As you can imagine the method `resolve` should return the value which is replaced inside your template.

Note on auto resolve:

Your class can but not need to append *Placeholder* to its name. For the auto resolver the class names `Foo` and `FooPlaceholder` are the same.

## Manually register a placeholder

To manually register a placeholder simply inject the `mindtwo\DocumentGenerator\Services\PlaceholderResolver` class into a container.
After that you can simply add a placeholder by calling `registerPlaceholder(string $name, Placeholder $placeholder)`.

e.g. to manually register our `FooPlaceholder`

```php
class ContainerClass {
    public function __construct(
        // ...,
        public PlaceholderResolver $placeholderResolver,
    )

    // ...

    public function func()
    {
        // ...

        $this->placeholderResolver->registerPlaceholder('bar', new FooPlaceholder());

        // ...
    }

    // ...
}
```

If a placholder happens to be registered under the name you try to register the placholder for the method will throw an `Exception`.
