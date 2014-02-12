<?php
    /**
     * @file   modules/integration_search/lang/jp.lang.php
     * @author nhn (developers@xpressengine.com) ??：RisaPapa、ミニミ
     * @brief  日本語言語パッケ?ジ（基本的な?容のみう）
     **/

    $lang->integration_search = '統合?索';

    $lang->sample_code = 'サンプルコ?ド';
    $lang->about_target_module = '選?されたモジュ?ルだけを?索?象とします。各モジュ?ルの?限設定にも注意して下さい。';
    $lang->about_sample_code = '上のコ?ドをレイアウトなどに?入すると統合?索が可能になります。';
    $lang->msg_no_keyword = '?索語を入力して下さい。';
    $lang->msg_document_more_search  = '??サ?チボタンを選?すると、まだ?索結果として引っかからなかった箇所を引き?き?索を行います。';

    $lang->is_result_text = "<strong>'%s'</strong>に?する?索結果<strong>%d</strong>件";
    $lang->multimedia = '?像/動?';
    
    $lang->include_search_target = '選?された?象のみ';
    $lang->exclude_search_target = '選?した?象を?索から除外';

    $lang->is_search_option = array(
        'document' => array(
            'title_content' => 'タイトル+?容',
            'title' => 'タイトル',
            'content' => '?容',
            'tag' => 'タグ',
        ),
        'trackback' => array(
            'url' => '?象URL',
            'blog_name' => '?象サイト（ブログ）名',
            'title' => 'タイトル',
            'excerpt' => '?容',
        ),
    );

    $lang->is_sort_option = array(
        'regdate' => '登?日',
        'comment_count' => 'コメント?',
        'readed_count' => '???',
        'voted_count' => '推薦?',
    );
?>
