//浏览器地址无限拼接参数
var all = "" ;
for (var i=0;i<1000;i++)
{
    all = all+i.toString();
    history.pushState(0,0,all);
}

//javascript运算
// 1.['a','b','c']+['d','e']
// 2.0.1+0.2
// 3.0.1 + 0.1 + 0.1 + 0.1 + 0.1 + 0.1 + 0.1 + 0.1 + 0.1 + 0.1
// 4.++[[]][+[]]+[+[]]   (自加为运算,其余为连接) eg: +[] number ,  +'' number , '' + '' string
// 5.{}+{}
// 6.undefined与null的区别
// 7.parseInt(0.0000008) === 8
// 8.为什么在函数里声明var a = b = 5;在函数外却能访问到b
// 1.1   "a,b,cd,e"
// 2.2    0.30000000000000004(17位小数点)
// 3.3	   0.9999999999999999(16位小数点)
// 4.4	   10
// 5.5    "[object Object][object Object]"


//js中变量污染
//1.利用匿名函数将脚本包裹起来(方法有很多种,自行百度)
(function(c){
    var a = 'a' ;
    var b = 'b'  ;
})(c);

void function(){
    alert('water');
}();//据说效率最高~
//2.定义全局变量命名空间,只创建一个全局变量，并定义该变量为当前应用容器,
    把其他全局变量追加在该命名空间下
var abc = {} ;
abc.abc = function() {alert(32)}
abc.abc()
//3.匿名
function f(){
    var i=0;
    function a(){
        alert(++i);
    }
    return a;
}

//判断一个单词是否是回文？
//回文是指把相同的词汇或句子，在下文中调换位置或颠倒过来，产生首尾回环的情趣，叫做回文，也叫回环 .
var str = "上海自来水来自海上" ;
//    1.str.length;i--,得到新的str去匹配
//2. array() 中有 reverse() 反转数组,
//    str->array->str
//split    join
function checkPalindrom(str)
{
    return str === str.split('').reverse().join('');
}


// 不借助临时变量，进行两个整数的交换
// 输入 a = 2, b = 4 输出 a = 4, b =2
// 这种问题非常巧妙，需要大家跳出惯有的思维，利用 a , b进行置换。
// 主要是利用 + – 去进行运算，类似 a = a + ( b – a) 实际上等同于最后 的 a = b;
function swap(a , b) {
    b = b - a;
    a = a + b;
    b = a - b;
    return [a,b];
}

//浏览器消息通知
if(window.Notification && Notification.permission !== "denied") {
    Notification.requestPermission(function(status) {
        let notify = new Notification('比甜通知', {
            body: '有消息啦！',
            icon: ''
        });
        notify.onclick = function(event) {
            //window.location.href = '/'
        };
    });
}

/*默认滚动到底部:部分无效*/
document.getElementById("table_div_fixed").scrollIntoView();

/*默认滚动到底部:有效果*/
var t = document.body.clientHeight;
window.scroll({ top: t, left: 0, behavior: 'smooth' });