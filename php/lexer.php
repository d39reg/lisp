<?php

	function tokenaze($code)
	{
		$return = [];
		$white = [' ',"\t","\n","\r"];
		$break = array_merge($white, ['(',')']);
		
		$l = strlen($code);
		for ($i = 0; $i < $l; $i++)
		{
			$s = $code[$i];
			if (in_array($s, $white)) continue;
			if ($s == '(' || $s == ')') $return[] = $s;
			else
			{
				$t = '';
				$e = $s == '"';
				do
				{
					$t .= $s;
					if (++$i >= $l) break;
					$s = $code[$i];
					if ($s == '"') $e = !$e;
				} while ($e || !in_array($s, $break));
				$i--;
				$return[] = $t;
			}
		}
		return $return;
	}
