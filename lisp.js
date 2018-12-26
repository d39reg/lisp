
function evalLisp(code)
{
	var i = 0;
	var global = {};
	var local = global;
	function chkWhite(s)
	{
		return s == ' ' || s == "\t" || s == "\r" || s == "\n";
	}
	function stdcall(name, args)
	{
		var l = args.length, i = 0;
		switch(name)
		{
			case '+':
				ret = args[0];i++;
				while(i < l) ret += args[i++];
				return ret;
			break;
			case '-':
				ret = args[0];i++;
				while(i < l) ret -= args[i++];
				return ret;
			break;
			case '*':
				ret = args[0];i++;
				while(i < l) ret *= args[i++];
				return ret;
			break;
			case '/':
				ret = args[0];i++;
				while(i < l) ret /= args[i++];
				return ret;
			break;
			case 'alert':
				while(i < l) alert(args[i++]);
			break;
			case 'print':
				while(i < l) document.write(args[i++]);
			break;
			case 'title':
				ret = args[0]; i++;
				while(i < l) ret += args[i++];
				document.title = ret;
			break;
			case 'input':
				var o = document.createElement('input');
				o.value = args[0];
				document.body.appendChild(o);
			break;
			case 'set':
				if(args[0].charAt(0) != '*')
				{
					global[args[0]] = args[1];
				}
				else local[args[0]] = args[1];
			break;
			case 'get':
				if(args[0].charAt(0) != '*')
				{
					return global[args[0]];
				}
				return local[args[0]];
			break;
		}
	}

	function e()
	{
		var argc = 0;
		var s = '';
		var name = '';
		var tmp = '';
		var args = [];

		while(i < code.length)
		{
			s = code.charAt(i++);
			while(chkWhite(s)) s = code.charAt(i++);
			if(s == ')') break;
			if(!argc)
			{
				while(!chkWhite(s) && s != ')' && i < code.length)
				{
					name += s;
					s = code.charAt(i++);
				}
				--i;
			}
			else
			{
				if(s == '(')
				{
					var o = global;
					local = [];
					tmp = e();
					local = o;
				}
				else if(s >= '0' && s <= '9')
				{
					tmp = '';
					while(s >= '0' && s <= '9')
					{
						tmp += s;
						s = code.charAt(i++);
					}
					tmp = parseInt(tmp);
					--i;
				}
				else if(s == '"')
				{
					s = code.charAt(i++);
					tmp = '';
					while(s != '"' && i < code.length)
					{
						tmp += s;
						s = code.charAt(i++);
					}
				}
				else
				{
					tmp = '';
					while(!chkWhite(s) && s != ')' && i < code.length)
					{
						tmp += s;
						s = code.charAt(i++);
					}
					--i;
				}

				args[args.length] = tmp;
			}
			argc++;
		}
		if(s != ')') return;
		return stdcall(name, args);
	}
	while(i<code.length)
	{
		while(chkWhite(code.charAt(i++)));
		if(code.charAt(i-1) != '(') return;
		local = global;
		e();
		if(code.charAt(i-1) != ')') return;
	}
}
window.onload = function()
{
	evalLisp('(set x 123)(print (+ (get x) 4))');
}

