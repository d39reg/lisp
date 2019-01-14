<?php
	class lisp
	{
		private $i = 0;
		private $global = Array();
		private $local = 0;
		private $l = 0;
		private $code = '';
		private function chkWhite($s)
		{
			return $s == ' ' || $s == "\t" || $s == "\r" || $s == "\n";
		}
		private function stdcall($name, $args, $types)
		{
			$this->l = count($args);
			$l = $this->l;
			$i = 0;
			switch($name)
			{
				case '+':
					$ret = $args[0];$i++;
					$str = gettype($ret) == 'string';
					for ($i = 1; $i < $l; $i++) 
					{
						if (!$str) $str = gettype($args[$i]) == 'string';
						if ($str) $ret .= $args[$i];
						else $ret += $args[$i];
					}
					return $ret;
				break;
				case '-':
					$ret = $args[0];$i++;
					while($i < $l) $ret -= $args[$i++];
					return $ret;
				break;
				case '*':
					$ret = $args[0];$i++;
					while($i < $l) $ret *= $args[$i++];
					return $ret;
				break;
				case '/':
					$ret = $args[0];$i++;
					while($i < $l) $ret /= $args[$i++];
					return $ret;
				break;
				case '==':
					return $args[0] == $args[1];
				break;
				case '&&':
					return $args[0] && $args[1];
				break;
				case '>':
					if($args[0] > $args[1]) return true;
					return false;
				break;
				case '<':
					if($args[0] < $args[1]) return true;
					return false;
				break;
				case 'print':
					while($i < $l) echo $args[$i++];
				break;
				case '.':
					return $args[0];
				break;
				case 'eval':
					
					for($i = 0; $i < count($args); $i++)
					{
						$this->eval($args[$i]);
					}
				break;
				case 'if':
					if($args[0]) return $args[1];
				break;
				case 'setq':
					if($args[0][0] != '*')
					{
						$this->global[$args[0]] = $args[1];
					}
					else $this->local[$args[0]] = $args[1];
				break;
				case 'get':
					if($args[0][0] != '*')
					{
						return $this->global[$args[0]];
					}
					return $this->local[$args[0]];
				break;
				case 'random':
					return rand($args[0],$args[1]);
				break;
				
				default:
					return call_user_func_array($name, $args);
				break;
			}
		}
		private function e()
		{
			$argc = 0;
			$s = '';
			$name = '';
			$tmp = '';
			$args = [];
			$types = [];
			while($this->i < strlen($this->code))
			{
				$s = $this->code[$this->i++];
				while($this->chkWhite($s)) $s = $this->code[$this->i++];
				if($s == ')') break;
				if(!$argc)
				{
					while(!$this->chkWhite($s) && $s != ')' && $this->i < strlen($this->code))
					{
						$name .= $s;
						$s = $this->code[$this->i++];
					}
					--$this->i;
				}
				else
				{
					if($s == '(')
					{
						$o = $this->global;
						$this->local = [];
						$tmp = $this->e();
						$this->local = $o;
						if(gettype($tmp) == 'string') $type = 2;
						elseif(gettype($tmp) == 'integer') $type = 1;
						else $type = 0;
					}
					elseif($s >= '0' && $s <= '9')
					{
						$tmp = '';
						while(($s >= '0' && $s <= '9') || $s == '.')
						{
							$tmp .= $s;
							$s = $this->code[$this->i++];
						}
						$tmp = (int)$tmp;
						--$this->i;
						$type = 1;
					}
					elseif($s == '"')
					{
						$s = $this->code[$this->i++];
						$tmp = '';
						while($s != '"' && $this->i < strlen($this->code))
						{
							$tmp .= $s;
							$s = $this->code[$this->i++];
						}
						$type = 2;
					}
					else
					{
						$tmp = '';
						while(!$this->chkWhite($s) && $s != ')' && $this->i < strlen($this->code))
						{
							$tmp .= $s;
							$s = $this->code[$this->i++];
						}
						--$this->i;
						$type = 3;
					}
					$args[count($args)] = $tmp;
					$types[count($types)] = $type;
				}
				$argc++;
			}
			if($s != ')') return;
			return $this->stdcall($name, $args, $types);
		}
		public function eval($code)
		{
			$tmpI = $this->i;
			$tmpL = $this->l;
			$tmpC = $this->code;
			$tmpG = $this->global;
			
			$this->i = 0;
			$this->l = strlen($code);
			
			$this->local = $this->global;
			$this->code = $code;
			
			while($this->i < strlen($code))
			{
				while($this->chkWhite($code[$this->i++]));
				if($code[$this->i-1] != '(') return;
				$this->local = $this->global;
				$this->e();
				if($code[$this->i-1] != ')') return;
			}
			
			$this->i      = $tmpI;
			$this->l      = $tmpL;
			$this->code   = $tmpC;
			$this->global = $tmpG;
		}
	}
	$l = new lisp();
	$l->eval('(eval "(print 1)(print 3)")(eval "(print 2)")');
	
?>
