var TString = 1
	,TSymbol = 2
	,TNumber = 3
	,TList   = 4;

function parser(code)
{
	function ast()
	{
		var token = code[0];
		code = code.slice(1);
	
		if (token == '\'') return [TList, [[TSymbol, '\''], ast()]];
		if (token == '(')
		{
			var list = [];
			while (code.length && code[0] != ')')
			{
				list[list.length] = ast(code);
				if (!code.length) return [TList, list];
			}
			code = code.slice(1);
			return [TList, list];
		}
		return atom(token);
	}
	return ast();
}

function atom(token)
{
	if (((token[0] == '-') && (token.length > 1) && (token[1] >= '0' && token[1] <= '9')) 
	|| (token[0] >= '0' && token[0] <= '9')) return [TNumber, parseFloat(token)];
	if (token[0] == '"') return [TString, token.substr(1, token.length-2)];
	return [TSymbol, token.toUpperCase()];
}
