<?php
namespace bookLog\buildApis ;
use Faker\Factory;

/** build sender data
 *  构建基础数据
 * Class ChapterOne
 * @package bookLog\buildApis
 */
class ChapterOne{

    public function build()
    {
        $buildFactory = Factory::create();
        $buildData = [
            'name'=>$buildFactory->name,
            'email'=>$buildFactory->email,
            'phone'=>$buildFactory->phoneNumber,
            'address'=>$buildFactory->address,
            'title'=>$buildFactory->title,
            'dateTime'=>$buildFactory->dateTime,
            'password'=>$buildFactory->password,
        ] ;
        return $buildData;
    }
}

