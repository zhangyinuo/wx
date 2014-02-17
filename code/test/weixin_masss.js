var page = require('webpage').create(),
    system = require('system'),
    host = 'https://mp.weixin.qq.com',
    server = 'https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN',
    //userlist = 'https://mp.weixin.qq.com/cgi-bin/contactmanagepage?t=wxm-friend&token=tokenval&lang=zh_CN&pagesize=1000&pageidx=0&type=0&groupid=0',
    data = 'username=Username&pwd=passwd&f=json&imgcode=';

	posturl = 'https://mp.weixin.qq.com/cgi-bin/masssend';
	postref = 'https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=tokenval&lang=zh_CN';
	postdata = 'type=1&content=msgval&sex=0&groupid=0&synctxweibo=0&synctxnews=0&country=&province=&city=&imgcode=&token=tokenval&lang=zh_CN&random=randval&f=json&ajax=1&t=ajax-response';

phantom.cookiesEnabled = true;

page.onConsoleMessage = function(msg) { 
	console.log(msg);
};

currand = Math.random();

data = data.replace(/Username/g, system.args[1]);
data = data.replace(/passwd/g, system.args[2]);

postdata = postdata.replace(/msgval/g, system.args[3]);

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
					postref = postref.replace(/tokenval/g, mts[1]);
					postdata = postdata.replace(/tokenval/g, mts[1]);
					postdata = postdata.replace(/randval/g, currand);
					page.customHeaders = {'Referer':postref};
					page.open(posturl, 'post', postdata, function(s1) {
						if(s1 !== 'success') {
							console.log('Unable to get posturl!');
							phantom.exit();
						} else {
							console.log(postdata);
							console.log(page.content);
							phantom.exit();
						}
					});
				}
			}
		});


	}
});

