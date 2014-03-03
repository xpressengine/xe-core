<?php if(!defined("__ZBXE__")) exit(); ?>
<div style="padding:10px">
	<h1 class="h1">XE 소개</h1>
	<p class="overview">XpressEngine = eXpress(표현하다) + press(발행하다) + Engine(기관, 장치) | XpressEngine은 누구나 쉽고 편하고 자유롭게 인터넷에서 표현과 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다. 급변하는 인턴넷 환경과 다양한 기능의 요구에 부응하기 위해 XpressEngine은 다음과 같은 특징을 가지고 있습니다.</p>
	<h2 class="h2">모듈형(조립식) 구조</h2>
	<p class="tx">웹사이트를 구성하는 요소들은 무척 다양합니다.<br />
		콘텐츠를 작성하는 WYSIWYG 에디터부터 작성된 콘텐츠를 여러 형태로 보여주는 게시판, 위키, 블로그 등과 같은 프로그램이 있으며 보다 체계적인 웹사이트 관리를 위한 회원 관리 기능 등 웹 사이트에는 많은 구성 요소들이 필요합니다. 또한, 이러한 기능들은 대부분 HTML/CSS로 표현되는데 사이트의 디자인이나 사용자의 필요에 따라 표현 양식을 달리 해야할 때도 있습니다.
		XpressEngine은 다양한 기능과 디자인에 대한 사용자들의 요구를 충족시키기 위해 각각의 기능과 디자인이 구조적으로 연결되는 모듈형 구조를 사용합니다. <br />
		이 때, 모듈, 애드온, 위젯 등의 요소에서 기능을 담당하며 디자인은 스킨을 통해 변화시킵니다.
	</p>
	<div class="structure">
		<table border="1" cellspacing="0" summary="XE 구조는 크게 디자인, 프로그램, 프레임웍으로 나뉜다.">
			<col /><col /><col width="450" />
			<tr>
				<th rowspan="4" scope="row" class="vr"><span>디자인</span></th>
				<th scope="row"><span>위젯 스타일</span></th>
				<td style="padding-top:19px;">동일한 위젯 스킨을 다양한 색상, 스타일로 꾸밀 수 있는 기능입니다. </td>
			</tr>
			<tr>
				<th scope="row"><span>위젯 스킨</span></th>
				<td>위젯 프로그램에서 생성된 콘텐츠를 출력하는 형태를 결정합니다. </td>
			</tr>
			<tr>
				<th scope="row"><span>모듈 스킨</span></th>
				<td>모듈 프로그램에서 생성된 콘텐츠를 출력하는 형태를 결정합니다. </td>
			</tr>
			<tr>
				<th scope="row" class="vr"><span>레이아웃</span></th>
				<td class="vr" style="padding-bottom:19px;">웹사이트의 틀을 출력합니다. </td>
			</tr>
			<tr>
				<th class="vr" rowspan="4" scope="row"><span>프로그램</span></th>
				<th scope="row"><span>애드온</span></th>
				<td style="padding-top:19px;">모듈 실행 이전이나 이후에 필요한 동작을 추가하는 비교적 간단한 코드입니다. </td>
			</tr>
			<tr>
				<th scope="row"><span>에디터 컴포넌트</span></th>
				<td>글 작성 WYSIWYG 에디터에 다양한 기능들을 추가할 수 있는 기능입니다. </td>
			</tr>
			<tr>
				<th scope="row"><span>위젯</span></th>
				<td>웹사이트의 곳곳에 다양한 콘텐츠를 출력할 수 있는 작은 프로그램입니다. </td>
			</tr>
			<tr>
				<th scope="row" class="vr"><span>모듈</span></th>
				<td class="vr" style="padding-bottom:19px;">게시판, 위키, 블로그 등 독립된 기능을 제공하는 프로그램입니다. </td>
			</tr>
			<tr>
				<th scope="row"><span>프레임워크</span> </th>
				<th scope="row"><span>XpressEngine Core</span> </th>
				<td style="padding:20px 0;">XpressEngine의 전체 동작을 관리하는 가장 기초가 되는 요소로, <br />
				요청을 분석하고 이에 따라 프로그램/스킨을 실행해 결과물을 출력합니다.</td>
			</tr>
		</table>
	</div>
		<p class="tx">XpressEngine은 위의 구조를 기반으로 Core를 통해 개별 프로그램과 스킨을 실행하여 결과물을 생성합니다.<br />
다양한 개성을 가지고 만들어진 프로그램, 스킨의 조합을 통해 다채로운 기능과 다양한 디자인을 가진 웹 사이트를 만들 수 있습니다.</p>
	<h2 class="h2"><img src="h2.open.gif" alt="오픈 소스 소프트웨어 / 오픈 소스 프로젝트" width="244" height="24" /></h2>
	<p class="tx">XpressEngine과 같은 CMS는 다루어야 할 영역이 너무 방대하기 때문에 특정 기업, 개인 또는 단체가 모든 기능을 개발하고 모든 문제점을 해결하기 힘듭니다. 이러한 한계를 극복하기 위해 XpressEngine은 LGPL v2라는 오픈소스 라이선스를 적용하여 개발자, 디자이너는 물론 사용자와의 협업을 이끌어낼 수 있는 오픈소스 프로젝트로 개발이 진행되고 있습니다. <a href="https://github.com/xpressengine" target="_blank">github.com의 프로젝트 페이지</a>를 통해 개발에 참여할 수 있습니다.<br />
XpressEngine 프로젝트의 참여에는 제한이 없습니다. 이미 많은 분들이 개발, 디자인, 마크업, 기획 등의 전문 분야는 물론 다국어 번역, 문제점 보고 등  기능 개선 및 문제점 해결을 위해 많은 노력을 해주고 계십니다. 참여를 원하시는 분은 XpressEngine 공식 웹사이트의 개발자 포럼 또는 각 프로그램/스킨별 프로젝트 사이트에 방문하시면 됩니다.</p>
	<h2 class="h2">XE 설치 환경</h2>

	<table border="1" cellspacing="0" class="install">
		<tr class="version">
			<th scope="col" colspan="2">XE 1.7.0 이상 </th>
		</tr>
		<tr>
			<th scope="col">PHP</th>
			<th scope="col" style="border-left:1px solid #fff;">데이터베이스</th>
		</tr>
		<tr>
			<td>
				<span>&bull;</span> PHP 5.2.4 이상<br />
				<span>&bull;</span> XML 라이브러리 - 필수<br />
				<span>&bull;</span> GD 라이브러리 - 필수<br />
				<span>&bull;</span> ICONV - 선택
			</td>
			<td style="border-left:1px solid #ebebeb;">
				<div style="float:left; width:160px;">
					<span>&bull;</span> CUBRID<br />
					<span>&bull;</span> MySQL 4.1 이상<br />
					<span>&bull;</span> MS-SQL<br />
				</div>
			</td>
	</table>
	<table border="1" cellspacing="0" class="install">
		<tr class="version">
			<th scope="col" colspan="2">XE 1.7.0 미만</th>
		</tr>
		<tr>
			<th scope="col">PHP</th>
			<th scope="col">데이터베이스</th>
		</tr>
		<tr>
			<td>
				<span>&bull;</span> PHP 4.x ~ 5.x<br />&nbsp;&nbsp;(PHP 5.2.2 버전 제외)<br />
				<span>&bull;</span> XML 라이브러리 - 필수<br />
				<span>&bull;</span> GD 라이브러리 - 필수<br />
				<span>&bull;</span> ICONV 선택
			</td>
			<td style="border-left:1px solid #ebebeb;">
				<div style="float:left; width:160px;">
					<span>&bull;</span> CUBRID<br />
					<span>&bull;</span> MySQL 4.1 이상<br />
					<span>&bull;</span> Firebird<br />
				</div>
				<div style="float:left; width:160px;">
					<span>&bull;</span> PostgreSQL<br />
					<span>&bull;</span> Sqlite3<br />
					<span>&bull;</span> MS-SQL
				</div>
			</td>
		</tr>
	</table>
</div>
