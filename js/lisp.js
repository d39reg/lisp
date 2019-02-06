var TSymbol  = 1;
var TString  = 2;
var TNumber  = 3;
var TList    = 4;
var Proc     = 5;
var Lambda   = 6;
var TObject  = 7;

var nil    = [TSymbol, 'NIL'];
var Ttrue  = [TSymbol, 'T'];
var Tfalse = [TSymbol, 'F'];

var globVariables = 
{
	'.':[Proc, function(x)
	{
		return x[0];
	}]
	,'+':[Proc, function(x)
	{
		if (!x.length) return nil;
		var n = x[0][1];
		for (i = 1; i < x.length; i++) n += x[i][1];
		return [TNumber, n];
	}]
	,'tag':[Proc, function(x)
	{
		return x.tag;
	}]
};

window.onload = function()
{
	var body = document.body.querySelectorAll('*');
	for(var i = 0; i < body.length; i++)
	{
		parseLisp(body[i]);
	}
}

function toStr(o, nvar)
{
	if (o[0] == TList)
	{
		var list = o[1];
		var s = '(';
		var l = list.length-1;
		for(var i = 0; i < l; i++)
		{
			s += toStr(list[i], true);
			if (l != i) s += ' ';
		}
		return s+')';
	}
	if (nvar) return o[1];
	switch (o[0])
	{
		case Lambda:
			return '[LAMBDA]';
		break;
		case Proc:
			return '[PROC]';
		break;
		case TObject:
			return '[OBJECT]';
		break;
		case TString:
		case TNumber:
			return o[1];
		break;
	}
	if (['T','F','NIL'].indexOf(o[1]) != -1) return o[1];
	if (o[1] in globVariables) return toStr(globVariables[o[1]]);
	//if (array_key_exists($x[1],$vars)) return toString($vars[$x[1]]);
	//echo "*** - {$GLOBALS['procName']}: variable {$x[1]} has no value";
	//exit;
}

function parseLisp(o)
{
	globVariables.self = [TObject, o];
	var text = o.innerText;
	var newText = '';
	var lisp = false;
	var lispCode = '';
	for(var i = 0; i < text.length; i++)
	{
		var s = text.charAt(i);
		if (s == '{' && text.charAt(i+1) == '(') 
		{ 
			lisp = true;
			lispCode += '(';
			i++; 
			continue;
		}
		else if (s == ')' && text.charAt(i+1) == '}') 
		{ 
			lisp = false;
			lispCode += ')';
			i++; 
			newText += evalLispCode(lispCode); 
			lispCode = ''; 
			continue; 
		}
		if (lisp) lispCode += s;
		else newText += s;
	}
	o.innerText = newText;
}

function evalLispCode(code)
{
	var lex = tokenaze(code);
	var ast = parser(lex);
	//console.log(ast);
	return toStr(evalLisp(ast), false);
}

function evalLisp(ast)
{
	
	if (ast[0] == TSymbol)
	{
		if (ast[1] in globVariables) return globVariables[ast[1]];
		return ast;
	}
	else if (ast[0] == TNumber)
	{
		return ast;
	}
	else if (ast[0] == TList)
	{
		var list = ast[1];
		if (!list.length) return nil;
		
		// функции не тербущие выполения аргументов!
		
		switch (list[0][1])
		{
			case '\'':
				return list[1];
			break;
			case 'LAMBDA':
				return [Lambda, list[1], list[2]];
			break;
			case 'IF':
				if (evalLisp(list[1])[1] == Tfalse)
				{
					if (count(list) < 4) return nil;
					return evalLisp(list[3]);
				}
				return evalLisp(list[2]);
			break;
		}
		
		// функции требущие выполения аргументов!
		exp = [];
		for (i = 0; i < list.length; i++) exp[exp.length] = evalLisp(list[i]);
		//if ($ast[1] == 'nil') return nil;

		if (exp[0][0] == Lambda)
		{
			e = [];
			for (i = 0; i < exp[0][1][1].length; i++)
			{
				if (i < exp.lenght-1) e[exp[0][1][1][i][1]] = exp[i+1];
				else e[exp[0][1][1][i][1]] = nil;
			}
			return evalLisp(exp[0][2][1], exp[0][3], e);
		}
		else if (exp[0][0] == Proc)
		{
			list = list.slice(1);
			var r = exp[0][1](list);
			if (r) return r;
			return nil;
		}
		//echo "*** - EVAL: функция {$exp[0][1]} не определена";
		//exit;
		return nil;
		
	}
}
