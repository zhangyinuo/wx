var page = require('webpage').create(),
    host = 'https://mp.weixin.qq.com',
    server = 'https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN',
    //userlist = 'https://mp.weixin.qq.com/cgi-bin/contactmanagepage?t=wxm-friend&token=tokenval&lang=zh_CN&pagesize=1000&pageidx=0&type=0&groupid=0',
    userlist = 'https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=100&pageidx=0&type=0&groupid=0&token=tokenval&lang=zh_CN',
    data = 'username=danezhang77@gmail.com&pwd=f8a4724578222780266930e86a3125b0&f=json&imgcode=';

phantom.cookiesEnabled = true;

page.onConsoleMessage = function(msg) { 
	console.log(msg);
};

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
					userlist = userlist.replace(/tokenval/g, mts[1]);
					//console.log(userlist);
					page.open(userlist, function(s1) {
						if(s1 !== 'success') {
							console.log('Unable to get userlist!');
							phantom.exit();
						} else {
							//console.log(page.content);
							page.evaluate(function() {      
								for(i = 0; i < wx.cgiData.friendsList.length; i++)
								{
									console.log(wx.cgiData.friendsList[i].id);    
								}
							});
							phantom.exit();
						}
					});
				}
			}
		});


	}
});

