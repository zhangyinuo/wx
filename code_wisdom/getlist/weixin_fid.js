var page = require('webpage').create(),
    host = 'https://mp.weixin.qq.com',
    server = 'https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN',
    system = require('system'),
    msglist = 'https:/mp.weixin.qq.com/cgi-bin/singlesendpage?tofakeid=fakeval&t=message/send&action=index&token=tokenval&lang=zh_CN';
    data = 'username=Username&pwd=passwd&f=json&imgcode=';
    //data = 'username=danezhang77@gmail.com&pwd=f8a4724578222780266930e86a3125b0&f=json&imgcode=';

phantom.cookiesEnabled = true;

page.onConsoleMessage = function(msg) { 
	console.log(msg);
};

data = data.replace(/Username/g, system.args[1]);
data = data.replace(/passwd/g, system.args[2]);
msglist = msglist.replace(/fakeval/g, system.args[3]);
page.open(host, function(r1) {
	if(r1 !== 'success') {
		console.log('Unable to open host');
		phantom.exit();
	} else {
		//console.log(page.content);
		page.customHeaders = {'Referer':'https://mp.weixin.qq.com/'};
		page.open(server, 'post', data, function (r2) {
			if (r2 !== 'success') {
				console.log('Unable to post login!');
				phantom.exit();
			} else {
				//console.log(page.content);
				var cnt = page.content;
				var ptn = /.*token=(\d+).*/;
				var mts = ptn.exec(cnt);
				if(mts != null) {
					msglist = msglist.replace(/tokenval/g, mts[1]);
					//console.log(msglist);
					page.open(msglist, function(s1) {
						if(s1 !== 'success') {
							console.log('Unable to get msglist!');
						} 
						else {
						console.log(page.content);
						phantom.exit();
						}
					});
				}
			}
		});
	}
});



