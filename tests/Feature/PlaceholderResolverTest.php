<?php

use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

it('can make PlaceholderResolver class', function () {
    $this->assertInstanceOf(PlaceholderResolver::class, app()->make(PlaceholderResolver::class));
});

it('can resolve all placeholders', function () {
    $resolver = app()->make(PlaceholderResolver::class);
    $model = new class() extends \Illuminate\Database\Eloquent\Model
    {
        public $name = 'John Doe';
    };

    $placeholders = [
        'name',
    ];

    $resolved = $resolver->resolveAll($placeholders, $model);

    expect($resolved)->toBe([
        'name' => 'John Doe',
    ]);
});

it('can resolve a single placeholder', function () {
    $resolver = app()->make(PlaceholderResolver::class);
    $model = new class() extends \Illuminate\Database\Eloquent\Model
    {
        public $name = 'John Doe';
    };

    $resolved = $resolver->resolve('name', $model);

    expect($resolved)->toBe('John Doe');
});

it('can resolve a placeholder instance', function () {
    $resolver = app()->make(PlaceholderResolver::class);

    $resolver->registerPlaceholder(new class() implements \mindtwo\DocumentGenerator\Modules\Placeholder\Contracts\Placeholder
    {
        public function resolve(\Illuminate\Database\Eloquent\Model $model, array $extra = []): null|string|\Stringable
        {
            return $model->name;
        }
    }, 'model_name');

    $model = new class() extends \Illuminate\Database\Eloquent\Model
    {
        public $name = 'John Doe';
    };

    $resolved = $resolver->resolve('model_name', $model);

    expect($resolved)->toBe('John Doe');
});

it('can resolve a placeholder from a multi placeholder instance', function () {
    $resolver = app()->make(PlaceholderResolver::class);

    $resolver->registerPlaceholder(new class() implements \mindtwo\DocumentGenerator\Modules\Placeholder\Contracts\PlaceholderMultiple
    {
        public function resolve(\Illuminate\Database\Eloquent\Model $model, array $extra = []): array
        {
            return [
                'model_name' => $model->name,
                'foo' => 'bar',
            ];
        }

        public function resolvedKeys(): array
        {
            return [
                'model_name',
                'foo',
            ];
        }
    });

    $model = new class() extends \Illuminate\Database\Eloquent\Model
    {
        public $name = 'John Doe';
    };

    $resolved = $resolver->resolve('model_name', $model);
    $resolved2 = $resolver->resolve('foo', $model);

    expect($resolved)->toBe('John Doe')->and($resolved2)->toBe('bar');
});

it('can resolve all with multiple instances', function () {
    $resolver = app()->make(PlaceholderResolver::class);

    $resolver->registerPlaceholder(new class() implements \mindtwo\DocumentGenerator\Modules\Placeholder\Contracts\PlaceholderMultiple
    {
        public function resolve(\Illuminate\Database\Eloquent\Model $model, array $extra = []): array
        {
            return [
                'model_name' => $model->name,
                'foo' => 'bar',
            ];
        }

        public function resolvedKeys(): array
        {
            return [
                'model_name',
                'foo',
            ];
        }
    });

    $resolver->registerPlaceholder(new class() implements \mindtwo\DocumentGenerator\Modules\Placeholder\Contracts\Placeholder
    {
        public function resolve(\Illuminate\Database\Eloquent\Model $model, array $extra = []): null|string|\Stringable
        {
            return $model->name.'2';
        }
    }, 'model_name2');

    $model = new class() extends \Illuminate\Database\Eloquent\Model
    {
        public $name = 'John Doe';
    };

    $placeholders = [
        'model_name',
        'foo',
        'model_name2',
    ];

    $resolved = $resolver->resolveAll($placeholders, $model);

    expect($resolved)->toBe([
        'model_name' => 'John Doe',
        'foo' => 'bar',
        'model_name2' => 'John Doe2',
    ]);
});
