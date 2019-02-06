function tokenaze(code)
{
	var r = [];
	var w = [' ',"\t","\n","\r"];
	var b = ['(',')',' ',"\t","\n","\r"];
	
	var l = code.length;
	for (var i = 0; i < l; i++)
	{
		var s = code.charAt(i);
		if (w.indexOf(s) > -1) continue;
		if (s == '(' || s == ')') r[r.length] = s;
		else
		{
			var t = '';
			var e = (s == '"');
			do
			{
				t += s;
				if (++i >= l) break;
				s = code.charAt(i);
				if (s == '"') e = !e;
			} while (e || !(b.indexOf(s) > -1));
			i--;
			r[r.length] = t;
		}
	}
	return r;
}
