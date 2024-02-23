<?php

use mindtwo\DocumentGenerator\Modules\Content\Blocks\BaseBlock;

// it('returns default dom when rendered', function () {
//     $block = new BaseBlock('test', 'test');

//     $this->assertEquals('test', $block->render([]));
// });

it('replaces placeholder when rendered', function () {
    $block = new BaseBlock('test', '<div>{test}</div>');

    $this->assertEquals('<div>bar</div>', $block->render([
        'test' => 'bar',
        'foo' => 'baz',
    ]));
});
