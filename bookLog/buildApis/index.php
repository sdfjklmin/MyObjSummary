<?php
namespace bookLog\buildApis ;
use Faker\Factory;

/** build sender data
 * Class ChapterOne
 */
class ChapterOne{

    public function build()
    {
        $buildData = Factory::create();
        dd($buildData);
    }

}
