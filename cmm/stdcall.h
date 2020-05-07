/*
	STDCALL function
	Author: PaulCodeman
*/

void initFunctionLisp()
{
	set_procedure("WINDOW", #lisp_window);
	set_procedure("LAMBDA", #lisp_lambda);
	set_procedure("TEST",  #lisp_test);
	set_procedure("LIST",  #lisp_list);
	set_procedure("EXIT",  #lisp_exit);
	set_procedure("SLEEP", #lisp_sleep);
	set_procedure("PRINT", #lisp_print);
	set_procedure("INPUT", #lisp_input);
	set_procedure("STDCALL", #lisp_stdcall);
	set_procedure("SETQ",   #lisp_setq);
	set_procedure("DEFVAR", #lisp_setq);
	set_procedure("+",     #lisp_add);
	set_procedure("-",     #lisp_sub);
	set_procedure("=",     #lisp_cmp);
}

dword lisp_exit(dword args)
{
	IF(initConsole) con_exit stdcall (1);
    ELSE ExitProcess();
}

dword lisp_lambda(dword args)
{
	malloc(sizeStruct);
	DSDWORD[EAX] = Lambda;
	DSDWORD[EAX+4] = args;
	return EAX;
}

dword objectWindow = 0;
dword new_window()
{
	dword id = 0;
	dword left = 250;
	dword top = 200;
	dword width = 350;
	dword height = 300;
	dword type = 0x34;
	dword background = 0xDED7CE;
	dword caption = "Title";
	dword keypress = 0;
	dword data = 0;
	dword param = 0;
	dword i = 0;

	loop()
	{
		i++;
		buffer = indexArray(objectWindow, i);
		IF (!DSDWORD[buffer]) break;
		buffer = DSDWORD[buffer]; // get param
		IF (DSDWORD[buffer] != TList) break;
		buffer = DSDWORD[buffer+4];
		param = indexArray(buffer, 0);
		IF (!DSDWORD[param]) break;
		data = indexArray(buffer, 1);
		IF (!DSDWORD[data]) break;
		param = crc32(string(DSDWORD[param]));
		data = lisp(DSDWORD[data]);
		if (crc32("WIDTH") == param) width = number(data);
		else if (crc32("HEIGHT") == param) height = number(data);
		else if (crc32("LEFT") == param) left = number(data);
		else if (crc32("TOP") == param) top = number(data);
		else if (crc32("TITLE") == param) caption = string(data);
		//else if (crc32("KEYPRESS") == param) keypress = function(data);
	}

	loop() switch(WaitEvent())
	{
		case evButton:
			id = GetButtonID();
			//IF (onbutton)onbutton(id);
			IF (id == 1) ExitProcess();
			break;

		case evKey:
			GetKeys();
			//IF(onkey)onbutton(key_scancode);
			//if (key_scancode == SCAN_CODE_ESC ) ExitProcess();
			break;

		case evReDraw:
			//GetProcessInfo(#Form, SelfInfo);

			EAX = 12;              // function 12:tell os about windowdraw
			EBX = 1;
			$int 0x40

			$xor EAX,EAX
			EBX = left << 16 + width;
			ECX = top << 16 + height;
			EDX = type << 24 | background;
			EDI = caption;
			ESI = 0;
			$int 0x40


			EAX = 12;              // function 12:tell os about windowdraw
			EBX = 2;
			$int 0x40
			//IF(ondraw)ondraw();
			break;
	}
}

dword lisp_window(dword args)
{
	dword thread = 0;
	objectWindow = args;
	thread = malloc(0x1000);
	EAX = 51;
	EBX = 1;
	ECX = #new_window;
	EDX = #new_window+0x1000;
	$int 0x40
	EAX = 5;
	EBX = 100;
	$int 0x40

}



dword lisp_test(dword args)
{
	malloc(sizeStruct);
	DSDWORD[EAX] = TString;
	DSDWORD[EAX+4] = "ZZZ";
	return EAX;
}

dword lisp_setq(dword args)
{
	dword i = 0;
	dword name = 0;
	dword data = 0;
	while(1)
	{
		i++;
		data = indexArray(args, i);
		data = DSDWORD[data];
		IF (!data) break;

		if (i&1)
		{
			name = DSDWORD[data+4];
		}
		else
		{
			set_variable(name, lisp(data));
		}
	}
	return 0;
}

dword lisp_print(dword args)
{
	dword i = 0;
	consoleInit();
	loop()
	{
		i++;
		indexArray(args, i);
		IF (!DSDWORD[EAX]) break;
		con_printf stdcall (string(lisp(DSDWORD[EAX])));
	}
	con_printf stdcall ("\r\n");
	return 0;
}

dword lisp_list(dword args)
{
	dword i = 0;
	dword list = 0;
	dword buffer = 0;
	list = malloc(32);
	loop()
	{
		i++;
		buffer = indexArray(args, i);
		IF (!DSDWORD[buffer]) break;
		indexArray(list, i-1);
        DSDWORD[EAX] = buffer;
	}
	malloc(sizeStruct);
	DSDWORD[EAX] = TList;
	DSDWORD[EAX+4] = list;
    return EAX;
}

dword lisp_stdcall(dword args)
{
	dword i = 0;
	dword buffer = 0;
	while(1)
	{
		i++;
		indexArray(args, i);
		buffer = DSDWORD[EAX];
		IF (!buffer) break;
		$push DSDWORD[buffer+4];
	}
	IF (i == 2) $pop eax
	IF (i == 3) $pop ebx
	IF (i == 4) $pop ecx
	$int 0x40
	return EAX;
}

dword lisp_input(dword args)
{
	dword buffer = 0;
	consoleInit();
	buffer = malloc(100);
	con_gets stdcall(buffer, 100);
	malloc(sizeStruct);
	DSDWORD[EAX] = TString;
	DSDWORD[EAX+4] = buffer;
	return EAX;
}

dword lisp_inc(dword args)
{
	dword i = 0;
	dword sum = 0;
	dword buffer = 0;
	while(1)
	{
		i++;
		buffer = indexArray(args, i);
		IF (!DSDWORD[buffer]) break;
		buffer = DSDWORD[buffer];
	}
	return 0;
}

dword lisp_add(dword args)
{
	dword i = 0;
	dword sum = 0;
	dword buffer = 0;
	while(1)
	{
		i++;
		buffer = indexArray(args, i);
		IF (!DSDWORD[buffer]) break;
		buffer = DSDWORD[buffer];

		sum += number(buffer);
	}
	malloc(sizeStruct);
	DSDWORD[EAX] = TNumber;
	DSDWORD[EAX+4] = sum;
	return EAX;
}

dword lisp_sub(dword args)
{
	dword i = 0;
	dword sum = 0;
	while(1)
	{
		i++;
		indexArray(args, i);
		IF (!DSDWORD[EAX]) break;
		sum -= number(DSDWORD[EAX]);
	}
	malloc(sizeStruct);
	DSDWORD[EAX] = TNumber;
	DSDWORD[EAX+4] = sum;
	return EAX;
}

dword lisp_cmp(dword args)
{
	dword i = 0;
	dword first = 0;
	dword buffer = 0;

	while(1)
	{
		i++;
		buffer = indexArray(args, i);
		buffer = DSDWORD[buffer];
		IF (!buffer) break;
		if (i == 1)
		{
			first = buffer;
		}
		else
		{
			if (DSDWORD[first+4] != DSDWORD[buffer+4])
			{
				malloc(sizeStruct);
				DSDWORD[EAX] = TSymbol;
				DSDWORD[EAX+4] = NIL;
				return EAX;
			}
		}
	}
	if (i == 1) error_message("*** - EVAL: too few arguments given to =: (=)");
	malloc(sizeStruct);
	DSDWORD[EAX] = TSymbol;
	DSDWORD[EAX+4] = "T";
	return EAX;
}

dword lisp_sleep(dword args)
{
	dword time = 0;
	indexArray(args, 1);
	time = number(DSDWORD[EAX]);
	EAX = 5;
	EBX = time;
	$int 0x40
	return 0;
}
