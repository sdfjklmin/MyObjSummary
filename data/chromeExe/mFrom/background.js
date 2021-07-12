console.log('into background js');

// 当用户按下tab chrome将输入交给插件，然后输入第一个字符之后触发此事件
chrome.omnibox.onInputStarted.addListener(() => {
    console.log('tab input');
});

// 当用户的输入改变之后
// text 用户的当前输入
// suggest 调用suggest为用户提供搜索建议
chrome.omnibox.onInputChanged.addListener((text, suggest) => {
    console.log("input onchange");
    // 为用户提供一些搜索建议
    suggest([
        {
            "content": "我是内容",
            "description": "我是描述"
        },
        {
            "content": "我是内容2",
            "description": "我是描述2"
        }
    ])
});

// 按下回车时事件，表示向插件提交了一个搜索
chrome.omnibox.onInputEntered.addListener((text, disposition) => {

    var baseLink = {

    };

    //内容处理，这里默认打开配置的地址
    if (baseLink[text] !== undefined) {
        window.open(baseLink[text]);
    }
});

// 取消输入时触发的事件，注意使用上下方向键在搜索建议列表中搜搜也会触发此事件
chrome.omnibox.onInputCancelled.addListener(() => {
    console.log("[" + new Date() + "] omnibox event: onInputCancelled");
});

// 当删除了搜索建议时触发的
chrome.omnibox.onDeleteSuggestion.addListener(text => {
    console.log("[" + new Date() + "] omnibox event: onDeleteSuggestion, text: " + text);
});

//设置默认的搜索建议，会显示在搜索建议列表的第一行位置，content省略使用用户当前输入的text作为content
chrome.omnibox.setDefaultSuggestion({
    "description": "这里是默认提示语"
})