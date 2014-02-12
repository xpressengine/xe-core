<?php
$method = Context::getRequestMethod();
if($method == 'POST')
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html lang="ko" xml:lang="ko" xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<meta name="Generator" content="XpressEngine 1.4.4.2" /> 
<meta name="module" content="opage" /> 
<meta http-equiv="imagetoolbar" content="no" /> 
<title>XE CAMP 2011</title> 
<?
    $vars = Context::getRequestVars();

    $logged_info = Context::get('logged_info');
    if($logged_info)
    {
        $args->member_srl = $logged_info->member_srl;
        $output = executeQuery('enroll.getCountEnrollByMemberSrl', $args);
        if($output->data->count>0){
    ?>
    <script type="text/javascript">//<![CDATA[
    alert('이미 참가신청 하셨습니다.');
    document.location.href="http://www.xpressengine.com";
    //]]></script>
    <?
        exit;

        }
    }

    $vars->enroll_srl = getNextSequence();
    $vars->member_srl = $logged_info->member_srl;
    $vars->user_name = $logged_info->user_name;
    $vars->nick_name = $logged_info->nick_name;
    $vars->user_id = $logged_info->user_id;
    
    $output = executeQuery('enroll.insertEnrollItem', $vars);
    if(!$output->toBool()) {
    ?>
    <script type="text/javascript">//<![CDATA[
    alert('에러가 발생하였습니다. 잠시후 다시 등록 부탁드립니다.');
    history.back();
    //]]></script>
    <?

    }else{
    ?>
    <script type="text/javascript">//<![CDATA[
    alert('참가신청 등록 되었습니다');
    document.location.href="http://www.xpressengine.com";
    //]]></script>
    <?
    }
?>
<body></body>
</html>

<?

    
    exit;
}

$ADDR = include(_XE_PATH_.'opages/add.php');
$r_addr1 = $_GET['addr1'];
if($r_addr1)
{
    $addr2 = $ADDR[$r_addr1];
    echo '<option value="">선택</option>';
    if($addr2) 
    {
        foreach($addr2 as $v) echo '<option value="'.$v. '">'.$v.'</option>';
    }
    exit;
}

?>
<style>
form legend { display:none; }
form fieldset { border:0; }
dt { font-size:14px; font-weight:bold; }
dd { margin:0 0 15px 0; }
</style>


<h1>두번째 XE CAMP가 열립니다!</h1>

<p>콘텐츠의 생산, 발행, 유통을 돕는 자유 오픈 소스 소프트웨어 XpressEngine에서 두번째 XE CAMP를 준비하였습니다.
사용자들의 소통과 배움의 장으로 거듭나기 위한 이번 XE CAMP는 개발자, 디자이너, 사용자 세션으로 구분되어 기초부터 고급 사용 기법까지 배울 수 있도록 체계적으로 구성된 교육 프로그램을 제공합니다.</p>

<p>이번 XE CAMP에는 여러분의 열정을 불태울 행사 속의 행사 XE Code CAMP도 준비되어있습니다.
XE CAMP에서 배운 내용을 실습을 통해 기초를 다지고, XE Code CAMP에서 팀 프로젝트를 통해 결과물을 만들어낸다면 정말 값진 경험이 될 것입니다.
또한, XE CAMP에는 XE 개발팀을 비롯한 여러 명의 멘토들이 항상 여러분의 곁에 있어 프로젝트를 진행하며 어려운 점은 바로 답을 얻을 수 있습니다.</p>

<p>무박 2일 동안 더 많이 배우고, 더 많이 느낄 수 있는 XE CAMP를 기대해주세요.
여러분의 많은 참여 기다리겠습니다.</p>

<h2>일정</h2>

<dl>
    <dt>참가 접수</dt>
    <dd>2010년 12월 6일 ~ 26일</dd>
    <dt>참석자 발표</dt>
    <dd>2011년 1월 10일</dd>
    <dt>XE CAMP</dt>
    <dd>2011년 1월 27일 ~ 28일</dd>
</dl>

<ol>
    <li class="n3">셔틀버스는 오전 11시 강남역에서 출발하여 CAMP 장소인 분당 서현동까지
        운행하며, 행사 후에는 다시 강남역으로 모셔 드립니다. </li>
    <li class="n4">튜토리얼은 다음 세 가지 중 하나를 선택하여 참석하실 수 있습니다.</li>
</ol>


<table border="1" cellspacing="0" class="t1" summary="XE 캠프 튜토리얼은 초급 과정과 고급 과정으로 나뉘는데 각 과정에 따른 대상, 목표, 자격 요건이 명시되어 있습니다.">
    <caption>
    XE 캠프 튜토리얼
    </caption>
    <thead>
        <tr>
            <th scope="col">시간</th>
            <th scope="col">개발자 트랙</th>
            <th scope="col">디자이너 트랙</th>
            <th scope="col">사용자 트랙</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>13:00</th>
            <td colspan="3">접수</td>
        <tr>
            <th>15:00 ~ 16:00</th>
            <td>XE 작동구조, 개발 콘셉트</td>
            <td>레이아웃과 스킨의 개념, 스킨의 동작 원리</td>
            <td>XE 기초 사용법1: 설치부터 개시판까지</td>
        </tr>
        <tr>
            <th>16:15 ~ 17:15</th>
            <td>모듈 개발 기초</td>
            <td>XE 기자인을 위한 HTML, CSS </td>
            <td>XE 기초 사용법2: 페이지, 위젯, 사이트 구성</td>
        </tr>
        <tr>
            <th>17:30 ~ 18:30</th>
            <td>모듈 개발 실습1 </td>
            <td>템플릿 문법, 게시판, 텍스타일의 템플릿 분석</td>
            <td>주요 모듈 소개</td>
        </tr>
        <tr>
            <th>20:00 ~ 22:00</th>
            <td>모듈 개발 실습2 </td>
            <td>스킨 제작</td>
            <td>사이트 제작 실습</td>
        </tr>
    </tbody>
</table>


<h2>참가신청</h2>

<form method="post" class="xe_camp" onsubmit="return checkValue(this);">
<input type="hidden" name="mid" value="{$mid}" />

<fieldset>
<legend>XE CAMP 참가 신청서</legend>
<dl>
    <dt> <label for="user_name">이름</label> </dt>
    <dd> <input name="user_name" id="user_name"/> </dd>

    <dt> <label for="email_address">이메일</label> </dt>
    <dd> <input name="email_address" id="email_address" size="40" /> </dd>

    <dt> <label for="cell_phone">핸드폰 번호</label> </dt>
    <dd> <input name="cell_phone" id="cell_phone" /> </dd>

    <dt> <label for="addr1">거주지</label> </dt>
    <dd>
        <select name="addr1" id="addr1">
            <option value="">선택</option>
            <?
            $addr1 = array_keys($ADDR);
            foreach($addr1 as $val) {
            ?>
            <option value="<?=$val?>"><?=$val?></option>
            <?}?>
        </select>
        <select name="addr2" id="addr2" style="display:none">
        </select>
    </dd>

    <dt> <label for="age">나이</label> </dt>
    <dd>
        <select name="age" id="age">
            <option value="">선택</option>
            <option value="~15">15세 미만</option>
            <option value="15~19">15세 ~ 19세</option>
            <option value="20~19">20세 ~ 24세</option>
            <option value="25~19">25세 ~ 29세</option>
            <option value="30~19">30세 ~ 34세</option>
            <option value="35~19">35세 ~ 39세</option>
            <option value="40~19">40세 ~ 44세</option>
            <option value="45~19">45세 ~ 49세</option>
            <option value="50~">50세 이상</option>
        </select>
    </dd>

    <dt> 성별 </dt>
    <dd>
        <input type="radio" name="sex" id="sex_m" value="M" /> <label for="sex_m">남자</label>
        <input type="radio" name="sex" id="sex_w" value="W" /> <label for="sex_w">여자</label>
    </dd>

    <dt> <label for="job">직업</label> </dt>
    <dd>
        <select name="job" id="job">
            <option value="">선택</option>
            <option value="디자이너">디자이너</option>
            <option value="퍼블리셔">퍼블리셔</option>
            <option value="개발자">개발자</option>
            <option value="학생">학생</option>
            <option value="기타">기타</option>
        </select>
    </dd>

    <dt> 참여 프로그램 트랙 </dt>
    <dd>
        <input type="radio" id="class_u" name="class" value="user" /> <label for="class_u">사용자 트랙</label>
        <input type="radio" id="class_s" name="class" value="skin" /> <label for="class_s">디자이너 트랙</label>
        <input type="radio" id="class_d" name="class" value="develop" /> <label for="class_d">개발자 트랙</label>
    </dd>

    <dt> 무박2일 참여 여부 </dt>
    <dd>
        <input type="radio" name="allnight" id="allnight_y" value="Y" /> <label for="allnight_y">참석</label>
        <input type="radio" name="allnight" id="allnight_n" value="N" /> <label for="allnight_n">미참석</label>
    </dd>

    <dt> <label for="content">참가신청 이유</label> </dt>
    <dd>
        <textarea name="content" cols="60" rows="5"></textarea>
    </dd>
</dl>

<span class="button large green"><button type="submit">신청하기</button></span>
</fieldset>
</form>
<script>
function checkValue(o){
    var user_name = jQuery('[name=user_name]',o);
    var email_address = jQuery('[name=email_address]',o);
    var cell_phone = jQuery('[name=cell_phone]',o);
    var addr1 = jQuery('[name=addr1] option:selected',o);
    var addr2 = jQuery('[name=addr2] option:selected',o);
    var age = jQuery('[name=age] option:selected',o);
    var job = jQuery('[name=job] option:selected',o);
    var sex = jQuery('[name=sex]:checked',o);
    var allnight = jQuery('[name=allnight]:checked',o);
    var xeclass = jQuery('[name=class]:checked',o);
    var content = jQuery('[name=content]',o);

    if(!user_name.val())
    {
        alert('이름을 입력해 주세요');
        user_name.focus();
        return false;
    }
   
   
    if(!email_address.val())
    {
        alert('이메일 주소를 입력해 주세요');
        email_address.focus();
        return false;
    }

    var email_filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if(!email_filter.test(email_address.val()))
    {
        alert('이메일 주소를 올바르게 입력해 주세요');
        email_address.val('').focus();
        return false;
    }

    
    var tem_cell_phone = cell_phone.val().replace(/[^0-9]/g,'');
    cell_phone.val(tem_cell_phone);
    if(!tem_cell_phone || tem_cell_phone.length<10)
    {
        alert('핸드폰 번호를 입력해 주세요');
        cell_phone.focus();
        return false;
    }

    if(!addr1.val())
    {
        alert('거주지 주소를 선택해 주세요');
        jQuery('[name=addr1]',o).focus();
        jQuery('[name=addr2]',o).hide();
        return false;
    }

    if(!addr2.val())
    {
        alert('거주지 주소를 선택해 주세요');
        jQuery('[name=addr2]',o).show().focus();
        return false;
    }

    if(!age.val())
    {
        alert('나이를 선택해 주세요');
        jQuery('[name=age]',o).focus();
        return false;
    }

    if(!sex.val())
    {
        alert('성별을 선택해 주세요');
        jQuery('[name=sex]',o).focus();
        return false;
    }


    if(!job.val())
    {
        alert('직업을 선택해 주세요');
        jQuery('[name=job]',o).focus();
        return false;
    }


    if(!xeclass.val())
    {
        alert('참여를 희망하시는 프로그램 트랙을 선택해 주세요');
        jQuery('[name=class]',o).focus();
        return false;
    }

    if(!allnight.val())
    {
        alert('무박2일 참여여부를 선택해 주세요');
        jQuery('[name=allnight]',o).focus();
        return false;
    }


    if(!content.val())
    {
        alert('참가신청 이유를 입력해 주세요');
        content.focus();
        return false;
    }


    return true;
}

jQuery(function($){
    $('#addr1').change(function(){
        var v = $('#addr1 option:selected').val();
        if(!v) return;
        $.get('./?mid=camp2011&addr1='+encodeURIComponent(v),
          function(data){
                $('#addr2').show().html(data);
            });
    });
});
</script>

