/**
 * SyntaxHighlighter
 * http://alexgorbatchev.com/SyntaxHighlighter
 *
 * SyntaxHighlighter is donationware. If you are using it, please donate.
 * http://alexgorbatchev.com/SyntaxHighlighter/donate.html
 *
 * @version
 * 3.0.83 (July 02 2010)
 * 
 * @copyright
 * Copyright (C) 2004-2010 Alex Gorbatchev.
 *
 * @license
 * Dual licensed under the MIT and GPL licenses.
 *
 * Made by KnDol
 * http://www.kndol.net/
 */
;(function()
{
	// CommonJS
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

	function Brush()
	{
		var commands =	'assoc append arp at attrib allow attach addusers associate auditpol arj awk ' +
						'break backup browstat call cd chdir cls color copy cacls chcp chkdsk cmd ' +
						'cmdsync command comp compact convert castoff caston chkdir chkvol cx capture ' +
						'chmod chown cat chgprint choice clip compreg compress cp ' +
						'date del dir debug diskcomp diskcopy doskey dspace dsrights dumpel drivers ' +
						'diruse diskmap disksave diskuse defptr delprof delsrv dh deltree ' +
						'echo endlocal erase exit edlin exe2bin expand endcap em2ms exetype ' +
						'ftype fastopen fc find findstr finger forcedos format ftp flagdir flag ' +
						'fcopy filever findgrp fixacls forfiles freedisk free goto graftabl graphics ' +
						'grant getmac getsid global grep help hostname ipconfig ifmember instsrv ' +
						'keyb kernprof kill label loadfix listdir logout login local logevent logoff logtime ls lha ' +
						'md mkdir move mem mode more map monitor munge mv ' +
						'nbtstat net netstat nslookup ntbackup nmenu nlist ncopy nprint nprinter ndir ' +
						'netsvc nlmon nltest now netdom ntrights oh ' +
						'path pause popd prompt pushd ping print psc purge pstat pulist pmon printmig passprop ' +
						'pathman permcopy perms pfmon pkzip pkunzip perl qbasic ' +
						'ren rename rd rdisk recover regedit regedt32 replace restore rmdir route rundll32 ' +
						'remove revoke rendir rights regfind regini raslist rasusers reg regback regdmp rexx ' +
						'rkill rmtshare robocopy regrest remote rar reboot runhide repreg ' +
						'set setlocal shift start share sort subst salvage session slist syscon send setpass ' +
						'settts systime showacls showdisk showgrps showmbrs shutdown shutgui suss sysdiff sc scanreg ' +
						'sclist scopy secadd sleep soon srvcheck srvinfo su subinacl setx ' +
						'time title type tracert tree tlist tcopy tbackup touch translate timeout timethis timezone tlist ' +
						'userlist usrstat usrtogrp ver verify vol version volinfo vi ' +
						'winmsd whoami wait waitfor wc winmsdp wntipcfg winipcfg xcopy xcacls xxcopy xxcopy16';
		var keywords =	'cmdextversion defined do else enabledelayedexpansion enableextensions errorlevel exist equ for gtr geq if in lss leq not neq on off';
		var devices =	'aux con com1 com2 com3 com4 lpt1 lpt2 lpt3 lpt4 lpt5 lpt6 lpt7 lpt8 nul prn';
		 var variables = 'ALLUSERSPROFILE APPDATA CommonProgramFiles COMPUTERNAME ComSpec DATE FP_NO_HOST_CHECK HOMEDRIVE ' +
						'HOMEPATH LOGONSERVER NUMBER_OF_PROCESSORS OS Path PATHEXT PROCESSOR_ARCHITECTURE PROCESSOR_IDENTIFIER ' +
						'PROCESSOR_LEVEL PROCESSOR_REVISION ProgramFiles PROGS PROMPT SANDBOX_DISK SANDBOX_PATH SESSIONNAME ' +
						'SystemDrive SystemRoot TEMP TIME TMP USERDNSDOMAIN USERDOMAIN USERNAME USERPROFILE windir';

		this.regexList = [
			//
			// REM Comments
			// :: Comments
			{ regex: /(^::|rem).*$/gmi,									css: 'comments' },
			//
			// "Strings"
			// 'Strings'
			// `Strings`
			// ECHO String
			{ regex: SyntaxHighlighter.regexLib.doubleQuotedString,		css: 'string' },
			{ regex: SyntaxHighlighter.regexLib.singleQuotedString,		css: 'string' },
			{ regex: /`(?:\.|(\\\`)|[^\``\n])*`/g,						css: 'string' },
			{ regex: /echo.*$/gmi,										css: 'string'},
			//
			// :Labels
			{ regex: /^:.+$/gmi,										css: 'color3' },
			//
			// %Variables%
			// !Variables!
			{ regex: /(%|!)\w+\1/gmi,									css: 'variable' },
			//
			// %%a variable Refs
			// %1 variable Refs
			{ regex: /%\*|%%?~?[fdpnxsatz]*[0-9a-z]\b/gmi,				css: 'variable' },
			//
			// commands
			{ regex: new RegExp(this.getKeywords(commands), 'gmi'),		css: 'functions' },
			//
			// keywords
			{ regex: new RegExp(this.getKeywords(keywords), 'gmi'),		css: 'keyword' },
			//
			// devices
			{ regex: new RegExp(this.getKeywords(devices), 'gim'), 		css: 'color2' },
		];

		this.forHtmlScript(SyntaxHighlighter.regexLib.aspScriptTags);
	};

	Brush.prototype	= new SyntaxHighlighter.Highlighter();
	Brush.aliases	= ['bat', 'cmd', 'batch', 'btm'];

	SyntaxHighlighter.brushes.Batch = Brush;

	// CommonJS
	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
