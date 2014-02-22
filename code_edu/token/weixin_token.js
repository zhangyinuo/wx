var page = require('webpage').create(),
    system = require('system'),
	token= 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=MYID&secret=MYKEY';

	phantom.cookiesEnabled = true;

page.onConsoleMessage = function(msg) { 
	console.log(msg);
};

token = token.replace(/MYID/g, system.args[1]);
token = token.replace(/MYKEY/g, system.args[2]);
page.open(token, function(r1) {
	if(r1 !== 'success') {
		console.log('Unable to open host');
		phantom.exit();
	} else {
		console.log(page.content);
		phantom.exit();
	}
});

