<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption"><i class="icon-reorder"></i></div>
    </div>
    <div class="portlet-body form">
        <form id='addForm' class="form-horizontal" action="<?php echo site_url($controller_url."add_save")?>">
            <div class="form-body">                              
                <div class="form-group">
                    <label class="col-md-3 control-label">产品</label>
                    <div class="col-md-8">
                        <?php foreach ($products as $key => $item): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="form-control-static">
                                <span style="font-size: 100%" class="label label-info">名称:<?php echo $item['name'] ?></span>
                                <span style="font-size: 100%" class="label label-danger">单价:<?php echo $item['price'] ?>元</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="col-md-2 control-label">数量:</label>
                                <div class="col-md-3">                                
                                    <input type="text" placeholder="数量" datatype="n" name="qty[]" class="form-control qty" value="1" />
                                    <input type="hidden" name="pid[]" value="<?php echo $item['id'] ?>" />
                                    <input class="price" type="hidden" name="price[]" value="<?php echo $item['price'] ?>" />
                                    <input class="" type="hidden" name="pname[]" value="<?php echo $item['name'] ?>" />
                                </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">订单号</label>
                    <div class="col-md-2">
                        <input class="form-control" name="order_sn" value="" datatype="*"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">开单人</label>
                    <div class="col-md-2">
                        <select name="biller" class="form-control">
                            <?php foreach ($operators as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">类型</label>
                    <div class="col-md-2">
                        <select name="type" class="form-control">
                            <?php foreach ($order_type as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">备注</label>
                    <div class="col-md-6">
                        <textarea name="remarks" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">订单总价</label>
                    <div class="col-md-6">
                        <input id="total_price" name="total_price" class="form-control" readonly />
                    </div>
                </div>
            </div>
            <div class="form-actions fluid">
                <div class="col-md-offset-3 col-md-9">
                    <input onclick="product_select()" type='button' id="btn_sub" class="btn green btn-lg" value='重新选择产品'/>
                    <input type='submit' id="btn_sub" class="btn blue btn-lg" value='保存'/>
                </div>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/plugins/bootstrap-datepicker/css/datepicker.css" />

<script type="text/javascript" src="<?=base_url()?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.zh-CN.js" charset="UTF-8"></script>

<script type="text/javascript">
function product_select(){
    LoadPageContentBody('<?=site_url($controller_url."product_select")?>');
}
$(function () {
    // DatePicker.init1();
    var $total_price = $('#total_price'),
        total_price = 0;
    $('#addForm').on('keyup blur focus', '.qty', function(){
        total_price = 0;
        $.each($('.qty'), function (index,item) {
            var qty = parseInt($(this).val()),
                price = parseFloat($('.price:eq('+index+')').val());
            total_price += qty * price;
        });
        $total_price.val(total_price);
    })

    var form = $("#addForm").Validform({
        btnSubmit: '#btn_sub',
        tiptype:4,
        ajaxPost:true,
        callback:function(response){
            if(response.status == "y"){            
                if(confirm('是否继续添加')){
                    LoadPageContentBody('<?=site_url($controller_url."product_select")?>');
                }else{
                    $('#order_view').click();
                }
            }
        }
    });    
})
</script>