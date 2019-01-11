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
		private function stdcall($name, $args)
		{
			$this->l = count($args);
			$l = $this->l;
			$i = 0;
			switch($name)
			{
				case '+':
					$ret = $args[0];$i++;
					while($i < $l) $ret += $args[$i++];
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
				case 'print':
					while($i < $l) echo $args[$i++];
				break;
				case 'set':
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
			}
		}
		private function e()
		{
			$argc = 0;
			$s = '';
			$name = '';
			$tmp = '';
			$args = [];

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
					}
					elseif($s >= '0' && $s <= '9')
					{
						$tmp = '';
						while($s >= '0' && $s <= '9')
						{
							$tmp .= $s;
							$s = $this->code[$this->i++];
						}
						$tmp = (int)$tmp;
						--$this->i;
					}
					elseif($s == '"')
					{
						$s = $this->code[$this->i++];
						$tmp = '';
						while($s != '"' && $i < strlen($this->code))
						{
							$tmp .= $s;
							$s = $this->code[$this->i++];
						}
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
					}
					
					$args[count($args)] = $tmp;
				}
				$argc++;
			}
			if($s != ')') return;
			return $this->stdcall($name, $args);
		}
		public function eval($code)
		{
			$this->i = 0;
			$this->global = Array();
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
		}
	}
	$l = new lisp();
	$l->eval("(print (+ 23 3))");
	
?>
