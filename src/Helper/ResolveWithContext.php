<?php

namespace mindtwo\DocumentGenerator\Helper;

use Illuminate\Support\Facades\Config;
use mindtwo\DocumentGenerator\Enums\ResolveContext;

class ResolveWithContext
{

    public static function preview(): void
    {
        Config::set('documents.context', ResolveContext::Preview);
    }

    public static function generate(): void
    {
        Config::set('documents.context', ResolveContext::Generate);
    }

    public static function reset(): void
    {
        Config::set('documents.context', null);
    }

    public static function use(ResolveContext $ctx, callable $callback): mixed
    {
        if ($ctx === ResolveContext::Preview) {
            self::preview();
        } elseif ($ctx === ResolveContext::Generate) {
            self::generate();
        }

        $returnValue = $callback();

        self::reset();

        return $returnValue;
    }
}
