<?php

namespace Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

class DDDBasicComplianceArchTest
{
    public function test_domain_does_not_depend_on_other_layers() : Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Domain'))
            ->shouldNotDependOn()
            ->classes(
                Selector::namespace('App'),
                Selector::namespace('Database')
            );
    }
}
