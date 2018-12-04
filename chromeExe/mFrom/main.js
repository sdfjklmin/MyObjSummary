//创建一个元素
var script = document.createElement('script');

//register.js就是你插件目录下的脚本文件,这个文件里的代码是可以访问页面的函数和变量的
script.src = chrome.extension.getURL('one.js');

//将脚本插入body元素中,此时register.js文件的内容就会自动加载并运行了
document.body.append(script);