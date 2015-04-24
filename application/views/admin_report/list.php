<?php $this->load->view('admin/table_head');?>
<div class="row">
    <div class="col-md-12">
        <div class='portlet box light-grey'>
            <div class="portlet-title">
                <div class="caption"><i class="icon-globe"></i>
                <?php echo $biller ? $operators[$biller] : ''; ?>
                <?php echo isset($year) ? $year.'年' : ''; ?>
                <?php echo isset($month) ? $month.'月' : ''; ?>
                报表
                </div>
                <div class="tools">
                    <a class="collapse" href="javascript:;"></a>
                </div>
            </div>
            <div class="portlet-body">            
                <div class="row">
                    <div class="col-md-12">                    
                        <form id="search" class="form-inline">
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" placeholder="月份" class="form-control" value="<?php echo isset($month) ? $month : "" ?>" name="month" id="month">
                                <span class="input-group-addon">月</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" placeholder="年份" class="form-control" value="<?php echo isset($year) ? $year : "" ?>" name="year" id="year">
                                <span class="input-group-addon">年</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <select name="biller" class="form-control">
                                    <option value="">选择开单人</option>    
                                    <?php foreach ($operators as $key => $value): ?>
                                    <option <?php echo option_select($biller,$key); ?>  value="<?php echo $key ?>"><?php echo $value ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <input type="button" value="搜索" class="btn btn-default" onclick="infoQuery()" >
                        </form>
                    </div>
                </div>
                <div class="table-scrollable">
                    <table class='table table-striped table-bordered table-hover'>
                        <thead>
                            <tr>
                                <th>产品名称</th>
                                <th>销售数量</th>
                                <th>产品销售总额</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($result as $key => $row):?>
                            <tr>
                                <td><?php echo $row['pname'] ?></td>
                                <td><?php echo $row['total_qty'] ?></td>
                                <td><?php echo $row['total_qty']*$row['price'] ?> 元</td>
                            </tr>
                        <?php endforeach;?>
                        <tr>
                            <td colspan="2">
                                <strong>
                                <?php echo $biller ? $operators[$biller] : ''; ?>
                                <?php echo isset($year) ? $year.'年' : ''; ?>
                                <?php echo isset($month) ? $month.'月' : ''; ?>
                                销售总额
                                </strong>
                            </td>
                            <td><?php echo $total_price ?> 元</td>
                        </tr>
                        </tbody>
                    </table>
                </div>                
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css">
<script type="text/javascript" src="<?=base_url()?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?=base_url()?>js/page.js"></script>
<script type="text/javascript">
function product_select(){
    LoadPageContentBody('<?=site_url($controller_url."product_select")?>');
}
function del(id, table){
    common_del('<?=site_url($controller_url."del")?>', id, table, "#order_view");
}
function infoQuery() {
    var formData = $('#search').serialize();
    if($('#month').val() && !$('#year').val()){
        alert('请选择那一年');
        return;
    }
    LoadPageContentBody('<?=site_url($controller_url)?>', formData);
}
jQuery(document).ready(function() {      
    TableAdvanced.init();
    $('#month').datetimepicker({
        language: "zh-CN",
        format: 'mm',
        startView: 3,
        minView: 3,
        maxView: 3,
    });
    $('#year').datetimepicker({
        language: "zh-CN",
        format: 'yyyy',
        startView: 4,
        minView: 4,
        maxView: 4,
    });
});
</script>
