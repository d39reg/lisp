<?php 
	
	$vars = [];
	
	function toString($x, $nvar = false, $loc = [])
	{
		global $vars;
		if ($x[0] == TList)
		{
			$list = $x[1];
			$s = '(';
			$l = count($list)-1;
			foreach ($list as $key => $val)
			{
				$s .= toString($val, true);
				if ($l != $key) $s .= ' ';
			}
			return $s.')';
		}
		if ($nvar) return $x[1];
		switch ($x[0])
		{
			case Lambda:
				return '[LAMBDA]';
			break;
			case Proc:
				return '[PROC]';
			break;
			case String:
			case Number:
				return $x[1];
			break;
		}
		if (in_array($x[1], ['T','F','NIL'])) return $x[1];
		if (array_key_exists($x[1],$loc)) return toString($vars[$x[1]]);
		if (array_key_exists($x[1],$vars)) return toString($vars[$x[1]]);
		echo "*** - {$GLOBALS['procName']}: variable {$x[1]} has no value";
		exit;
	}

	function proc_ret($env, $arg, $loc)
	{
		if (count($arg) == 1) return nil;
		if (count($arg) == 2) return $arg[1];
		$r = [];
		for ($i = 1; $i < count($arg); $i++) $r[] = $arg[$i];
		return [TList, $r];
	}
	
	function proc_add($env, $arg, $loc)
	{
		$n = $arg[0][1];
		for ($i = 1; $i < count($arg); $i++) $n += $arg[$i][1];
		return [Number, $n];
	}
	
	function proc_sub($env, $arg, $loc)
	{
		$n = $arg[0][1];
		for ($i = 1; $i < count($arg); $i++) $n -= $arg[$i][1];
		return [Number, $n];
	}
	
	function proc_mul($env, $arg, $loc)
	{
		$n = $arg[1][1];
		for ($i = 2; $i < count($arg); $i++) $n *= $arg[$i][1];
		return [Number, $n];
	}
	
	function proc_cat($env, $arg, $loc)
	{
		$n = $arg[1][1];
		for ($i = 2; $i < count($arg); $i++) $n .= $arg[$i][1];
		return [Number, $n];
	}
	
	function proc_qt($env, $arg, $loc)
	{
		return $arg[1];
	}
	
	function proc_rvs($env, $arg, $loc)
	{
		$arg[1][1] = array_reverse($arg[1][1]);
		return $arg[1];
	}
	
	function proc_car($env, $arg, $loc)
	{
		return $arg[1][1][0];
	}
	
	function proc_cdr($env, $arg, $loc)
	{
		$o = $arg[1][1];
		array_shift($o);
		return [TList, $o];
	}
	
	function proc_div($env, $arg, $loc)
	{
		$n = $arg[1][1];
		for ($i = 2; $i < count($arg); $i++) $n /= $arg[$i][1];
		return [Number, $n];
	}
	
	function proc_prt($env, $arg, $loc)
	{
		//print_r($loc);
		for ($i = 1; $i < count($arg); $i++) echo toString($arg[$i], false, $loc);
		return nil;
	}
	
	function proc_setq($env, $arg, $loc)
	{
		global $vars;
		$len = count($arg);
		if ($len%2 == 0)
		{
			echo "*** - {$GLOBALS['procName']}: нечетное количество аргументов";
			exit;
		}
		for ($i = 1; $i < $len; $i++)
		{
			if ($i%2 == 1) 
			{
				if ($arg[$i][0] == Symbol) $name = $arg[$i][1];
				else 
				{
					echo "*** - {$GLOBALS['procName']}: {$arg[$i][1]} не является символом.";
					exit;
				}
			}
			else $vars[$name] = $arg[$i];
		}
		return nil;
	}
	
	function proc_bgn($env, $arg, $loc)
	{
		return $arg[count($arg)-1];
	}
	
	function proc_rav($env, $arg, $loc)
	{
		$len = count($arg);
		if ($len == 1) return Tfalse;
		if ($len == 2)
		{
			if($arg[1]) return Ttrue;
			return Tfalse;
		}
		
		for ($i = 1; $i < $len-1; $i++)
		{
			if ($arg[$i] != $arg[$i+1]) return Tfalse;
		}
		return Ttrue;
	}
	
	function proc_nrav($env, $arg, $loc)
	{
		$r = proc_rav($env, $arg, $loc);
		if ($r[0] == Symbol && $r[1] == 'T') return Tfalse;
		return Ttrue;
	}
	
	function proc_bol($env, $arg, $loc)
	{
		$len = count($arg);
		if ($len == 1) return Tfalse;
		if ($len == 2)
		{
			if($arg[1]) return Ttrue;
			return Tfalse;
		}
		
		for ($i = 1; $i < $len-1; $i++)
		{
			if ($arg[$i] <= $arg[$i+1]) return Tfalse;
		}
		return Ttrue;
	}
	function proc_bolr($env, $arg, $loc)
	{
		$len = count($arg);
		if ($len == 1) return Tfalse;
		if ($len == 2)
		{
			if($arg[1]) return Ttrue;
			return Tfalse;
		}
		
		for ($i = 1; $i < $len-1; $i++)
		{
			if ($arg[$i] < $arg[$i+1]) return Tfalse;
		}
		return Ttrue;
	}
	
	function proc_nbol($env, $arg, $loc)
	{
		$len = count($arg);
		if ($len == 1) return Tfalse;
		if ($len == 2)
		{
			if($arg[1]) return Ttrue;
			return Tfalse;
		}
		
		for ($i = 1; $i < $len-1; $i++)
		{
			if ($arg[$i] >= $arg[$i+1]) return Tfalse;
		}
		return Ttrue;
	}
	
	function proc_nbolr($env, $arg, $loc)
	{
		$len = count($arg);
		if ($len == 1) return Tfalse;
		if ($len == 2)
		{
			if($arg[1]) return Ttrue;
			return Tfalse;
		}
		
		for ($i = 1; $i < $len-1; $i++)
		{
			if ($arg[$i] > $arg[$i+1]) return Tfalse;
		}
		return Ttrue;
	}
	function proc_ext($env, $arg, $loc)
	{
		
		return Ttrue;
	}
	
	function addenv(&$env)
	{
		$env['.'] = [Proc, 'proc_ret'];
		$env['+'] = [Proc, 'proc_add'];
		$env['-'] = [Proc, 'proc_sub'];
		$env['*'] = [Proc, 'proc_mul'];
		$env['/'] = [Proc, 'proc_div'];
		$env['='] = [Proc, 'proc_rav'];
		$env['!='] = [Proc, 'proc_nrav'];
		$env['>'] = [Proc, 'proc_bol'];
		$env['<'] = [Proc, 'proc_nbol'];
		$env['>='] = [Proc, 'proc_bolr'];
		$env['<='] = [Proc, 'proc_nbolr'];
		$env['PRINT'] = [Proc, 'proc_prt'];
		$env['SETQ'] = [Proc, 'proc_setq'];
		$env['BEGIN'] = [Proc, 'proc_bgn'];
		$env['CONCAT'] = [Proc, 'proc_cat'];
		$env['QUOTE'] = [Proc, 'proc_qt'];
		$env['FIRST'] = $env['car'] = [Proc, 'proc_car'];
		$env['REST'] = $env['cdr'] = [Proc, 'proc_cdr'];
		$env['REVERSE'] = [Proc, 'proc_rvs'];
		$env['EXIT'] = [Proc, 'proc_ext'];
	}
