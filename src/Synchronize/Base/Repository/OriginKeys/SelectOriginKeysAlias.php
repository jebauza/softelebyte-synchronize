<?php


namespace Softelebyte\Synchronize\Base\Repository\OriginKeys;


use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\OriginKeyAlias;
use Softelebyte\Synchronize\Base\Contracts\Repo\SelectOriginKeys;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginKey;

class SelectOriginKeysAlias implements SelectOriginKeys
{
    public function processKeys(OriginKey $object): array
    {
        $originKeys = $object->originKey();
        if ($object instanceof OriginKeyAlias) {
            $originKeyAlias = $object->originKeyAlias();
            foreach ($originKeys as $i => $originKey) {
                if ($originKey instanceof Expression) {
                    $originKeys[$i] = $originKey->getValue(DB::getQueryGrammar()) . ' as ' . $originKeyAlias[$i];
                    $originKeys[$i] = DB::raw($originKeys[$i]);
                } else {
                    $originKeys[$i] = $originKey . ' as ' . $originKeyAlias[$i];
                }
            }
        }
        return $originKeys;
    }
}
