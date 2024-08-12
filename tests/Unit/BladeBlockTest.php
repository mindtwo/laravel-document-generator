<?php

use mindtwo\DocumentGenerator\Modules\Content\Blocks\BladeBlock;

it('returns default dom when rendered', function () {
    $block = new BladeBlock('test', 'test');

    $rendered = trim(preg_replace('/\s\s+/', '', $block->render([])));

    $this->assertEquals('<div>Test Block</div>', $rendered);
});

it('can resolve a template via dotted path', function () {
    $block = new BladeBlock('test', 'path.to.test');

    $rendered = trim(preg_replace('/\s\s+/', '', $block->render([])));

    $this->assertEquals('<div>Test Block</div>', $rendered);
});

it('resolves placeholder in template', function () {
    $block = new BladeBlock('test', 'template-placeholder');

    $placeholder = $block->placeholder();

    $this->assertEquals(['test'], $placeholder);
});

it('replaces placeholder when rendered', function () {
    $block = new BladeBlock('test', 'template-placeholder');

    $rendered = trim(preg_replace('/\s\s+/', '', $block->render([
        'test' => 'bar',
        'foo' => 'baz',
    ])));

    $this->assertEquals('<div>bar</div>', $rendered);
});

it('resolves placeholder in props', function () {
    $block = new BladeBlock('test', 'props-placeholder');

    $placeholder = $block->placeholder();

    $this->assertEquals(['foo', 'bar'], $placeholder);
});

it('resolves placeholder in template and props', function () {
    $block = new BladeBlock('test', 'props-template-placeholder');

    $placeholder = $block->placeholder();

    $this->assertEquals(['test', 'foo', 'bar'], $placeholder);
});
