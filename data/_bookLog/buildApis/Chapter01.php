<?php
namespace bookLog\buildApis ;
use Faker\Factory;

/**
 * @remark
 * 快速构建安全可用的基础数据
 */
/** build sender data
 *  构建基础数据
 * Class ChapterOne
 * @package bookLog\buildApis
 */
class Chapter01{

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

