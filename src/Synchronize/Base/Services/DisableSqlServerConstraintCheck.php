<?php


namespace Softelebyte\Synchronize\Base\Services;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Services\CallBackService;

class DisableSqlServerConstraintCheck implements CallBackService
{

    public function handle(): \Closure
    {
        dump('Disabling constraint checks');
        DB::unprepared(
            'EXEC sp_MSforeachtable \'ALTER TABLE ? NOCHECK CONSTRAINT all\';'
        );

        return function () {
            dump('Enabling constraint checks');
            DB::unprepared(
                'EXEC sp_MSforeachtable \'ALTER TABLE ? CHECK CONSTRAINT all\';'
            );
        };
    }
}