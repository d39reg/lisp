<?php

	function parser(&$code)
	{
		$token = array_shift($code);
		if ($token == '\'') return [TList, [[Symbol, '\''], parser($code)]];
		if ($token == '(')
		{
			$list = [];
			while ($code[0] != ')')
			{
				$list[] = parser($code);
				if (!count($code)) return [TList, $list];
			}
			array_shift($code);
			return [TList, $list];
		}
		return atom($token);
	}
	
	function atom($token)
	{
		if ((($token[0] == '-') && (count($token) > 1) && ($token[1] >= '0' && $token[1] <= '9')) 
		|| ($token[0] >= '0' && $token[0] <= '9')) return [Number, (float)$token];
		if ($token[0] == '"') return [String, substr($token, 1, strlen($token)-2)];
		return [Symbol, strtoupper($token)];
	}
