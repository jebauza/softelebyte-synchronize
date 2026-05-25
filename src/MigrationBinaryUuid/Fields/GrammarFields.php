<?php
namespace Softelebyte\MigrationBinaryUuid\Fields;


class GrammarFields
{
    const GRAMMARS = [
        self::MYSQL_GRAMMAR,
        self::POSTGRES_GRAMMAR,
        self::SQLITE_GRAMMAR
    ];
    const MYSQL_GRAMMAR = 'MySqlGrammar';
    const POSTGRES_GRAMMAR = 'PostgresGrammar';
    const SQLITE_GRAMMAR = 'SQLiteGrammar';
    const SQL_SERVER_GRAMMAR = 'SqlServerGrammar';
}