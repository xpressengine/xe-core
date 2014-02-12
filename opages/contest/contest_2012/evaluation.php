<?php
    if(!defined("__ZBXE__")) exit();
?>
<!-- #contest_evaluation -->
<?PHP
if(date('Ymd')>'20100110'){
?>
                <div id="contest_evaluation">
<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Evaluation.gif" width="95" height="50" alt="투표하기" /></h1>
                    <div class="notYet">
                        <dl class="tx1">
                            <dt>오늘 오후 2시부터 투표가 시작됩니다.</dt>
                            <dd>&nbsp;</dd>
                        </dl>
                        <dl class="tx2">
                            <dt>투표 기간</dt>
                            <dd>2010년 1월 11일 (월) - 1월 17일 (일)</dd>
                        </dl>
                    </div>
                </div>
 
<?PHP
}else{
?>


                <div id="contest_evaluation">
<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Evaluation.gif" width="95" height="50" alt="투표하기" /></h1>
{@ $targetDate = ztime("20100111"); }
{@ $gap = $targetDate - time(); }
{@ $days = (int)($gap/24/60/60) + 1; }
{@ $gap -= $days*24*60*60; }
{@ $hrs = (int)($gap/60/60); }
                    <div class="notYet">
                        <dl class="tx1">
                            <dt>지금은 응모작 준비 기간입니다.</dt>
                            <dd>{$days}일 후에 투표와 심사를 시작합니다.</dd>
                        </dl>
                        <dl class="tx2">
                            <dt>투표 기간</dt>
                            <dd>2010년 1월 11일 (월) - 1월 17일 (일)</dd>
                        </dl>
                    </div>
                </div>
                <!-- /#contest_evaluation -->
<?PHP
}
?>
