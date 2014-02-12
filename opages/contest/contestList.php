<?php
   $obj = Context::getRequestVars();

   $oModuleModel = &getModel('module');
   $contestUploadInfo = $oModuleModel->getModuleInfoByMid('contestUpload');

   $args->member_srl = $obj->member_srl;
   $args->module_srl = $contestUploadInfo->module_srl;
   $args->order_type = 'asc';
   $args->sort_index = 'regdate';

   $oDocumentModel = &getModel('document');
   $contestList = $oDocumentModel->getDocumentList($args);

   $contestList = $contestList->data;

   Context::set('contestList',$contestList);
   //var_Dump($contestList);


?>
<!--%import("css/default.css")-->


<div class="board">
    <div class="boardHeader">
		<div class="boardTitle">
			<h2 class="boardTitleText"><a href="/index.php?mid=contestProposal&amp;listStyle=webzine"><img src="img_content/img_h2_txt2.gif" width="102" height="54" alt="공모작 등록" /></a></h2>
		</div>
	</div>

    <div class="boardInformation">
		<div class="infoSum">공모작<strong>1</strong></div>
    </div>

    
<form class="boardListForm" method="get" action="./"><input type="hidden" value="/index.php?mid=contestProposal&amp;listStyle=webzine" name="error_return_url"><input type="hidden" value="" name="act"><input type="hidden" value="contestProposal" name="mid"><input type="hidden" value="" name="vid">
    <fieldset>
        <legend>List of Articles</legend>
        <table cellspacing="0" border="1" class="boardList webZine" summary="List of Articles">
			<thead>
                <tr>				    							
					<th scope="col">No.</th>								    														
					<th class="title" scope="col">Subject</th>	    														
				    <th scope="col">Date</th>									    														
					<th scope="col">Manage</a></th>						         
				</tr>
			</thead>
			<tbody>
				{@ $index_n = 1;}
				<tr class="bg1" loop="$contestList => $key, $val">
					{@ $extra_vars = $val->get('extra_vars');
					   $contest_info = unserialize($extra_vars);
					}
					<td>{$index_n}</td>
					<td class="title">
						<p class="title">
							<a class="title" href="/index.php?mid=contestProposal&amp;listStyle=webzine&amp;document_srl=113">{$contest_info->product_intro}</a>
						</p>
						<ul class="meta">                                                                            
							<li class="author"><a onclick="return false" class="member_4" href="#popup_menu_area">{$val->get('nick_name')}</a></li>                            <li class="jpg_label">jpg: <a href="{$contest_info->jpg_file}" class="jpg_link">{$contest_info->up_thumbnail['name']}</a></li>
							<li class="psd_label">psd: <a href="{$contest_info->psd_file}" class="psd_link">{$contest_info->up_psd['name']}</a></li>
                        </ul>					
					</td>
					<td>{zdate($val->get('regdate'), 'Y-M-d')}</td>
					<td><a href="{getUrl('','mid','contestUpload','document_srl',$val->get('document_srl'))}">update</a></td>
					{@ $index_n += 1;}
				</tr>
			</tbody>
        </table>
		
    </fieldset>
</form>

<div class="boardNavigation">
    <div class="buttonRight">
        <a class="buttonOfficial" href="{getUrl('','mid','contestUpload')}"><span>Write</span></a>
    </div>

    <div class="pagination">
        <a class="prevEnd" href="/index.php?mid=contestProposal&amp;listStyle=webzine">First Page</a> 
           <strong>1</strong> 
       <a class="nextEnd" href="/index.php?mid=contestProposal&amp;listStyle=webzine&amp;page=1">Last Page</a>
    </div>

</div>

<form class="boardSearchForm" id="fo_search" onsubmit="return procFilter(this, search)" method="get" action="http://contest.dev.xpressengine.cn/"><input type="hidden" value="" name="act">
    <fieldset>
        <legend>Board Search</legend>
        <input type="hidden" value="" name="vid">
        <input type="hidden" value="contestProposal" name="mid">
        <input type="hidden" value="" name="category">
        <input type="text" title="Search" accesskey="S" class="inputText" value="" name="search_keyword">
        <span class="buttonOfficial"><button onclick="xGetElementById('fo_search').submit();return false;" type="submit">Search</button></span>
    </fieldset>
</form>

</div>