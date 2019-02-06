<?php

	define('Symbol', 1);
	define('String', 2);
	define('Number', 3);
	define('TList' , 4);
	define('Proc'  , 5);
	define('Lambda', 6);
	
	define('nil',    [Symbol, 'NIL']);
	define('Ttrue',  [Symbol, 'T']);
	define('Tfalse', [Symbol, 'F']);
	
	$GLOBALS['procName'] = '';
	
	include('std.php');
	include('lexer.php');
	include('ast.php');

	function evalLisp($ast, &$env, &$loc = [])
	{
		if ($ast[0] == Symbol)
		{
			if (array_key_exists($ast[1], $loc)) return $loc[$ast[1]];
			if (array_key_exists($ast[1], $env)) return $env[$ast[1]];
			return $ast;
		}
		elseif ($ast[0] == Number)
		{
			return $ast;
		}
		elseif ($ast[0] == TList)
		{
			$list = $ast[1];
			if (!count($list)) return nil;
			
			// функции не тербущие выполения аргументов!
			
			switch ($list[0][1])
			{
				case '\'':
					return $list[1];
				break;
				case 'LAMBDA':
					return [Lambda, $list[1], $list[2], $env];
				break;
				case 'IF':
					if (evalLisp($list[1], $env)[1] == Tfalse)
					{
						if (count($list) < 4) return nil;
						return evalLisp($list[3], $env);
					}
					return evalLisp($list[2], $env);
				break;
			}
			
			// функции тербущие выполения аргументов!
			$exp = [];
			for ($i = 0; $i < count($list); $i++) $exp[] = evalLisp($list[$i], $env, $loc);

			//if ($ast[1] == 'nil') return nil;

			if ($exp[0][0] == Lambda)
			{
				$e = [];
				for ($i = 0; $i < count($exp[0][1][1]); $i++)
				{
					if ($i < count($exp)-1) $e[$exp[0][1][1][$i][1]] = $exp[$i+1];
					else $e[$exp[0][1][1][$i][1]] = nil;
				}
				return evalLisp($exp[0][2][1], $exp[0][3], $e);
			}
			elseif ($exp[0][0] == Proc)
			{
				$GLOBALS['procName'] = $list[0][1];
				return $exp[0][1]($env, $exp, $loc);
			}
			echo "*** - EVAL: функция {$exp[0][1]} не определена";
			exit;
			return nil;
			
		}

	}
	
	
	$tokens = tokenaze(file_get_contents('../../test.lisp'));
	//print_r($tokens);exit;
	
	$ast = parser($tokens);
	//print_r($ast);exit;
	$env = [];
	addenv($env);
	evalLisp($ast, $env);
	//print_r($env);
