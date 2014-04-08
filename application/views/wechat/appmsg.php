<?php $this->load->view('admin/table_head');?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box light-grey">
			<div class="portlet-title">
				<div class="caption"><i class="icon-reorder"></i>列表</div>
				<div class="actions">
				    <a onclick="addNews()" class="btn blue" ><i class="icon-plus"></i> 添加图文</a>
				</div>
			</div>
			<div class="portlet-body">
				<table class='table table-striped table-bordered table-hover Ctable' id="sample_1">
                <thead>
                    <tr>
                        <th>图片</th>
                        <th width="250">标题</th>
                        <th width="400">摘要</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($result as $key => $row):?>
                    <tr>
                        <td><div style="width:100px"><img class="img-responsive" src="<?=$row['picurl']?>" alt="<?=$row['title']?>"></div></td>
                        <td><?=$row['title']?></td>
                        <td><?=$row['description']?></td>
                        <td>
                        	<a target="_blank" class="btn blue" href="<?=$row['url']?>">预览</a>
                            <a class="btn green" onclick="editNews(<?=$row['id']?>)"> <i class="icon-pencil icon-white"></i> 编辑</a>
                            <a class="btn red" onclick="delNews(<?=$row['id']?>)"><i class="icon-remove icon-white"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {       
    TableAdvanced.init();
});
function addNews(){
    LoadPageContentBody('<?=site_url($controller_url."newsadd/")?>', {});
}
function editNews(id){
    LoadPageContentBody('<?=site_url($controller_url."newsedit/")?>', {id: id});
}
function delNews(id, code){
    common_del('<?=site_url($controller_url."newsdel")?>', id, code, '#wechat_appmsg_view');
}
</script>