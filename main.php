<?php
/**
 * Plugin Name: UEditor wp
 * Plugin URI: http://www.jiuciyu.com
 * Version: 1.4.3
 * Author: 大山, SamLiu, taoqili
 * Author URI: http://www.jiuciyu.com
 * Description: 强大的百度开源富文本编辑器UEditor正式登陆wordpress！此插件最早由taoqili开发，SamLiu改进,但两位作者均不再发布更新版本，大山在此基础上更新到1.4.3的最新版本。有任何问题欢迎登陆树新风www.shuxinfeng.cn使用交流。
 */
@include_once( dirname( __FILE__ ) . "/ueditor.class.php" );
if ( class_exists( "UEditor" ) ) {
    $ueditor_lang = 'en';
    if( stripos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'zh-cn') !== false){
        $ueditor_lang = 'zh-cn';
    }
    $ue = new UEditor("postdivrich",array(
        //此处可以配置编辑器的所有配置项，配置方法同editor_config.js
        "focus"=>true,
		"catchRemoteImageEnable"=>true,//设置是否抓取远程图片
		"initialFrameHeight"=>'500',
		"enableAutoSave"=>true,//启用自动保存
        "saveInterval"=>"10000", //自动保存间隔时间， 单位ms
        "textarea"=>"content",
        "zIndex"=>1,
        'lang'=>$ueditor_lang
    ));
    register_activation_hook( __FILE__, array(  &$ue, 'ue_closeDefaultEditor' ) );
    register_deactivation_hook( __FILE__, array(  &$ue, 'ue_openDefaultEditor' ) );
    add_action("wp_head",array(&$ue,'ue_importSyntaxHighlighter'));
    add_action("wp_footer",array(&$ue,'ue_syntaxHighlighter'));
    add_action("admin_head",array(&$ue,'ue_importUEditorResource'));
    add_action('edit_form_advanced', array(&$ue, 'ue_renderUEditor'));
    add_action('edit_page_form', array(&$ue, 'ue_renderUEditor'));
    add_action( 'plugins_unload', array(&$ue, 'ue_openDefaultEditor'));

    add_filter('the_editor', 'enable_ueditor');
}
function enable_ueditor($editor_box){
    if( strpos($editor_box, 'wp-content-editor-container') > 0 ){
        $js=<<<js_enable_ueditor
        <script type="text/javascript">
                var ueditor_container = document.getElementById('postdivrich');
                var editor_content = document.getElementById('content');
                var ueditor_content_container = document.createElement('script');
                var wp_ueditor_content = editor_content.defaultValue;
                ueditor_container.appendChild(ueditor_content_container);
                ueditor_content_container.setAttribute('id', 'postdivrich');
                ueditor_content_container.setAttribute('class', 'postarea');
                ueditor_content_container.setAttribute('type', 'text/plain');
                ueditor_container.removeAttribute('id');
                ueditor_container.removeAttribute('class');
                var mce_container = document.getElementById("wp-content-wrap");
                mce_container.parentNode.removeChild(mce_container);
        </script>
js_enable_ueditor;
        return $editor_box.$js;
    }
    return $editor_box;
}
