<?php
	class lisp
	{
		private $i = 0;
		private $global = Array();
		private $local = 0;
		private $l = 0;
		private $code = '';
		private $codeParser = [];
		private function chkWhite($s)
		{
			return $s == ' ' || $s == "\t" || $s == "\r" || $s == "\n";
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
			return [$name, $args];
		}
		public function parser($code)
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
				$this->codeParser[] = $this->e();
				if($code[$this->i-1] != ')') return;
			}
			return $this->codeParser;
		}
		
	}
	$l = new lisp();
	print_r($l->parser("(print (get x) (+ 1 2))"));
	
?>
