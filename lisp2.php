<?php
	
		function tokenaze($code)
		{
			$return = [];
			$break = ['(',')',' ',"\t","\n","\r"];
			$white = [' ',"\t","\n","\r"];
			$l = strlen($code);
			for ($i = 0; $i < $l; $i++)
			{
				$s = $code[$i];
				if (in_array($s, $white)) continue;
				if ($s == '(' || $s == ')') $return[] = $s;
				else
				{
					$t = '';
					do
					{
						$t .= $s;
						if (++$i >= $l) break;
						$s = $code[$i];
					} while ($i < $l && !in_array($s, $break));
					$i--;
					$return[] = $t;
				}
			}
			return $return;
		}
		
		function atom($token)
		{
			if ((($token[0] == '-') && ($token[1] >= '0' && $token[1] <= '9')) || ($token[0] >= '0' && $token[0] <= '9')) return (int)$token;
			return $token;
		}
		function parser(&$code)
		{
			$token = array_shift($code);
			if ($token == '(')
			{
				$list = [];
				while ($code[0] != ')')
				{
					$list[] = parser($code);
					if (!count($code)) return $list;
				}
				array_shift($code);
				return $list;
			}
			return atom($token);
		}
		function evalLisp($ast, &$env)
		{
			if (gettype($ast) == 'string')
			{
				if (array_key_exists($ast, $env)) return $env[$ast];
				return 0;
			}
			elseif (gettype($ast) == 'integer')
			{
				return $ast;
			}
			elseif (gettype($ast) == 'array')
			{
				if (!count($ast)) return 0;
				switch ($ast[0])
				{
					case 'quote':
						return $ast[1];
					break;
					case 'print':
						echo $ast[1];
						return;
					break;
					case 'set!':
						return $env[$ast[1]] = evalLisp($ast[2], $env);
					break;
				}
			}
		}
		$tokens = tokenaze('(print 12)');
		$ast = parser($tokens);
		$env = [];
		print_r(evalLisp($ast, $env));