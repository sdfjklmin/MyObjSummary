var express = require('express'),
    app = express(),
    PORT = 4158;

app.get('/', function(req, res){
    res.send('this is node js');

});
app.listen(PORT, function(){
    console.log('服务器已经启动～～～');
});


